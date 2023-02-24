<?php


namespace Codilar\CashOnDelivery\Plugin\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\OfflinePayments\Model\Cashondelivery as Subject;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Psr\Log\LoggerInterface;
use Codilar\WareIQ\Model\Resolver\EstimationDeliveryDate;

class Cashondelivery
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EstimationDeliveryDate
     */
    private $estimationDeliveryDate;
    /**
     * @var \Codilar\WareIQ\Model\Config
     */
    private $config;

    /**
     * Cashondelivery constructor.
     * @param ProductCollectionFactory $productCollectionFactory
     * @param LoggerInterface $logger
     * @param EstimationDeliveryDate $estimationDeliveryDate
     * @param \Codilar\WareIQ\Model\Config $config
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        LoggerInterface $logger,
        EstimationDeliveryDate $estimationDeliveryDate,
        \Codilar\WareIQ\Model\Config $config
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->logger = $logger;
        $this->estimationDeliveryDate = $estimationDeliveryDate;
        $this->config = $config;
    }

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param CartInterface|null $quote
     * @return mixed
     */
    public function aroundIsAvailable(
        Subject $subject,
        callable $proceed,
        CartInterface $quote = null
    )
    {
        $isAvailable = $proceed($quote);
        if ($isAvailable) {
            try {
                $this->validate($quote);
            } catch (\Exception $exception) {
                $isAvailable = false;
            }
        }
        return $isAvailable;
    }

    /**
     * @param CartInterface $quote
     * @throws LocalizedException
     * @throws \Codilar\WareIQ\Exception\PincodeNotServiceableException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    protected function validate(CartInterface $quote)
    {
        if ($this->isCustomerBlacklisted($quote->getCustomer())) {
            throw new LocalizedException(__('Customer is blacklisted'));
        }
        $items = $quote->getAllVisibleItems();
        $pincode = $quote->getBillingAddress()->getPostcode();
        $params = [
            'pincode' => $pincode,
            'sku_list' => []
        ];
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            $product = $this->productCollectionFactory->create()->addFieldToFilter('sku', $item->getSku())->addAttributeToSelect('*')->getFirstItem();

            $isFreshAttribute = $product->getCustomAttribute('is_fresh');
            if ($isFreshAttribute && $isFreshAttribute->getValue() == 1) {
                throw new LocalizedException(__('COD not available for fresh products'));
            }

            $wareIqSku = $product->getData('wareiq_sku');
            if ($wareIqSku === null) {
                $this->logger->info(__('"%1" does not have wareiq sku set', $item->getSku()));
                continue;
            }

            $params['sku_list'][] = [
                'sku' => $wareIqSku,
                'quantity' => $item->getQty()
            ];
        }

        if (!$this->config->isBypassWareIq()) {

            if (!count($params['sku_list'])) {
                throw new LocalizedException(__('No valid products in cart'));
            }

            if ($pincode) {
                $response = $this->estimationDeliveryDate->pincodeServiceabilityCheck($pincode, $params);

                $codAvailable = $response['data']['cod_available'] ?? false;
                $splitOrder = $response['data']['split_order'] ?? false;

                if ($splitOrder || !$codAvailable) {
                    throw new LocalizedException(__('COD not possible for order'));
                }
            }
        }
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    protected function isCustomerBlacklisted($customer)
    {
        $isBlacklistedAttribute = $customer->getCustomAttribute('is_blacklisted');
        if ($customer->getId() && $isBlacklistedAttribute && $isBlacklistedAttribute->getValue() == 1) {
            return true;
        }
        return false;
    }
}
