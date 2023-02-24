<?php

namespace Codilar\OrderReview\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Item;
use NexPWA\CatalogGraphQl\Helper\Product as ProductHelper;
use NexPWA\SalesGraphQl\Helper\Resolver\Order as OrderResolverHelper;
use Magento\Sales\Api\ShipmentItemRepositoryInterface;

class OrderTrackingResolver implements ResolverInterface
{

    public $shipmentArray = [];

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;
    /**
     * @var OrderResolverHelper
     */
    private OrderResolverHelper $orderResolverHelper;

    /**
     * @var ProductHelper
     */
    protected ProductHelper $productHelper;

    /**
     * @var AppState
     */
    protected AppState $appState;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected ShipmentRepositoryInterface $shipmentRepositoryInterface;

    /**
     * @var ShipmentItemRepositoryInterface
     */
    protected ShipmentItemRepositoryInterface $shipmentItem;

    /**
     * OrderItemResolver constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderResolverHelper $orderResolverHelper
     * @param ProductHelper $productHelper
     * @param AppState $appState
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ShipmentRepositoryInterface $shipmentRepositoryInterface
     * @param ShipmentItemRepositoryInterface $shipmentItem
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderResolverHelper $orderResolverHelper,
        ProductHelper $productHelper,
        AppState $appState,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentRepositoryInterface $shipmentRepositoryInterface,
        ShipmentItemRepositoryInterface $shipmentItem
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderResolverHelper = $orderResolverHelper;
        $this->productHelper = $productHelper;
        $this->appState = $appState;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentRepositoryInterface = $shipmentRepositoryInterface;
        $this->shipmentItem = $shipmentItem;
    }


    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null)
    {
        $customerId = $context->getUserId();
        if ($customerId === null || $customerId === 0) {
            throw new GraphQlAuthorizationException(__('User not found'));
        }
        $orderId = $args['id'] ?? null;
        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId() != $customerId) {
            throw new LocalizedException(__('Access denied'));
        }
        $data = [];

        foreach ($order->getItems() as $item) {
            $trackNumbers = [];
            $shipmentItemSearchCriteria = $this->searchCriteriaBuilder->addFilter('order_item_id', $item->getItemId())->create();
            $shipmentItemsData = $this->shipmentItem->getList($shipmentItemSearchCriteria);
            $shipmentItems = $shipmentItemsData->getItems();
            foreach ($shipmentItems as $shipmentItem) {
                $shipment = $this->checkShipment($shipmentItem);
                $trackNumbers = $this->getTracks($shipment,$trackNumbers);
            }
            $product = $item->getProduct();
            $options = [];
            foreach ($this->getItemOptions($item) as $option) {
                $values = explode(',', $option['value']);
                $values = array_map(function ($option) {
                    return trim($option);
                }, $values);
                $options[] = [
                    'label' => $option['label'],
                    'values' => $values
                ];
            }

            $status = $this->getStatus($item);

            $data[] = [
                'item_id' => $item->getItemId(),
                'is_product_exists' => $product !== null,
                'product_id' => $product ? $product->getId() : null,
                'url_path' => $product ? $product->getUrlKey() : null,
                'name' => $item->getName(),
                'sku' => $item->getSku(),
                'price' => $item->getPrice(),
                'qty_ordered' => $item->getQtyOrdered(),
                'qty_invoiced' => $item->getQtyInvoiced(),
                'qty_canceled' => $item->getQtyCanceled(),
                'qty_shipped' => $item->getQtyShipped(),
                'row_total' => $item->getRowTotal(),
                'thumbnail' => $this->getProductThumbnailImage($product),
                'additional_options' => $options,
                'track_number' => $trackNumbers,
                'status' => $status['status_code'],
                'status_label' => $status['status'],
                'model' => $item
            ];
        }

        return ['order_track_item' => $data];
    }

    public function checkShipment($shipmentItem)
    {
        if (isset($this->shipmentArray[$shipmentItem->getParentId()])) {
            $shipment = $this->shipmentArray[$shipmentItem->getParentId()];
        } else {
            $shipmentSearchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $shipmentItem->getParentId())->create();
            $shipment = $this->shipmentRepositoryInterface->getList($shipmentSearchCriteria);
            $this->shipmentArray[$shipmentItem->getParentId()] = $shipment;
        }

        return $shipment;
    }

    public function getTracks($shipment,$trackNumbers)
    {
        $shipmentDataItems = $shipment->getItems();
        foreach ($shipmentDataItems as $shipmentDataItem) {
            $tracks = $shipmentDataItem->getTracks();
            foreach ($tracks as $track) {
                $trackNumbers[] = $track->getTrackNumber();
            }
        }
        return $trackNumbers;

    }

    public function getStatus($item)
    {
        if ($item->getQtyShipped() == 0 && $item->getQtyInvoiced() == 0) {
            $status = 'Pending Payment';
            $status_code = 'pending_payment';
        } elseif ($item->getQtyShipped() == 0 && $item->getQtyInvoiced()) {
            $status = 'Invoiced';
            $status_code = 'invoiced';
        } elseif ($item->getQtyShipped() == $item->getQtyInvoiced()) {
            $status = 'Complete';
            $status_code = 'complete';
        } else {
            $status = 'Processing';
            $status_code = 'processing';
        }
        return ['status' => $status, 'status_code' => $status_code];
    }

    /**
     * Get item options.
     *
     * @param Item $item
     * @return array
     */
    protected function getItemOptions($item)
    {
        $result = [];
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * @param Product $product
     * @return mixed|string
     */
    protected function getProductThumbnailImage($product)
    {
        try {
            return $this->appState->emulateAreaCode(Area::AREA_FRONTEND, [$this->productHelper, 'getProductImage'], [$product]);
        } catch (\Exception $e) {
            return '';
        }
    }
}
