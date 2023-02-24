<?php

namespace Codilar\SalesOrder\Observer;

use Codilar\WareIQ\Exception\PincodeNotServiceableException;
use JsonSchema\Exception\RuntimeException;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Codilar\WareIQ\Model\Resolver\EstimationDeliveryDate;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class OrderPlaceBefore implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var EstimationDeliveryDate
     */
    private EstimationDeliveryDate $estimationDeliveryDate;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param EstimationDeliveryDate $estimationDeliveryDate
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        EstimationDeliveryDate $estimationDeliveryDate,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory
    ) {
        $this->estimationDeliveryDate = $estimationDeliveryDate;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws PincodeNotServiceableException
     * @throws ValidationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        $pincode = $order->getShippingAddress()->getPostcode();
        $params = [
            'pincode' => $pincode,
        ];

        $freshProductSkus = [];

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $this->collectionFactory->create()->addFieldToFilter('sku', $item->getSku())->addAttributeToSelect('*')->getFirstItem();

            $WareIqSku = $product->getData('wareiq_sku');
            if ($WareIqSku === null) {
                $this->logger->info(__('"%1" does not have wareiq sku set', $item->getSku()));
                continue;
            }

            $params['sku_list'][] = [
                'sku' => $WareIqSku,
                'quantity' => $item->getQtyOrdered()
            ];

            $isFreshAttribute = $product->getCustomAttribute('is_fresh');
            if ($isFreshAttribute && $isFreshAttribute->getValue() == 1) {
                $freshProductSkus[] = $WareIqSku;
            }
        }

        try {
            if (!empty($params['sku_list'])) {
                $deliveryDataResponse = $this->estimationDeliveryDate->pincodeServiceabilityCheck($pincode, $params, $freshProductSkus);
                $status = $deliveryDataResponse['success'] ?? null;
                if ($status === true) {
                    $totalDeliveryDate = $deliveryDataResponse['data']['delivery_date'] ?? "N/A";
                    $order->setData("order_expected_delivery_date", $totalDeliveryDate);
                    $this->saveOrderDeliveryDate($deliveryDataResponse['data']['sku_wise'] ?? [], $order);
                }
            }
        } catch (RuntimeException $runtimeException) {
            $this->logger->info($runtimeException->getMessage());
        }
    }

    /**
     * @param $deliveryData
     * @param $order
     */
    public function saveOrderDeliveryDate($deliveryData, $order)
    {
        try {
            $wareIqSkus = array_column($deliveryData, "delivery_date", "sku");
            /** @var OrderItem $item */
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $product = $this->collectionFactory->create()->addFieldToFilter('sku', $orderItem->getSku())->addAttributeToSelect('*')->getFirstItem();
                $wareIqSku = $product->getData('wareiq_sku');
                if (array_key_exists($wareIqSku, $wareIqSkus)) {
                    $orderItem->setData('order_item_expected_delivery_date', $wareIqSkus[$wareIqSku]);
                }
                if (!empty($wareIqSkus[$wareIqSku])) {
                    $options = $orderItem->getProductOptions();
                    $options['additional_options'][] = [
                        'label' => 'Estimated delivery date',
                        'value' => $wareIqSkus[$wareIqSku]
                    ];
                    $orderItem->setProductOptions($options);
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->info($localizedException->getMessage());
        }
    }
}
