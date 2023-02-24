<?php


namespace Codilar\Razorpay\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollectionFactory;

class AbstractOrderStatusSource implements OptionSourceInterface
{
    /**
     * @var OrderStatusCollectionFactory
     */
    private $orderStatusCollectionFactory;
    /**
     * @var string
     */
    private $state;

    /**
     * PaymentPendingStatuses constructor.
     * @param OrderStatusCollectionFactory $orderStatusCollectionFactory
     * @param string $state
     */
    public function __construct(
        OrderStatusCollectionFactory $orderStatusCollectionFactory,
        string $state
    )
    {
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->state = $state;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        /** @var \Magento\Sales\Model\Order\Status[] $statuses */
        $statuses = $this->orderStatusCollectionFactory->create()->addStateFilter($this->state)->getItems();
        $response = [];
        foreach ($statuses as $status) {
            $response[] = [
                'label' => $status->getLabel(),
                'value' => $status->getStatus()
            ];
        }
        return $response;
    }
}
