<?php


namespace Codilar\Razorpay\Setup\Patch\Data;


use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

class OrderStatuses implements DataPatchInterface
{
    /**
     * @var StatusFactory
     */
    private $statusFactory;
    /**
     * @var StatusResource
     */
    private $statusResource;

    /**
     * OrderStatuses constructor.
     * @param StatusFactory $statusFactory
     * @param StatusResource $statusResource
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResource $statusResource
    )
    {
        $this->statusFactory = $statusFactory;
        $this->statusResource = $statusResource;
    }

    /**
     * @inheridoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheridoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheridoc
     */
    public function apply(): OrderStatuses
    {
        $this->createOrderStatus('razorpay_payment_pending', __('Payment pending (Razorpay)'), 'pending_payment');
        $this->createOrderStatus('razorpay_payment_success', __('Payment successful (Razorpay)'), 'processing');
        $this->createOrderStatus('razorpay_payment_failed', __('Payment failed (Razorpay)'), 'canceled');
        return $this;
    }

    protected function createOrderStatus($code, $label, $state, $visibleOnFront = true)
    {
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => $code,
            'label' => $label
        ]);
        try {
            $this->statusResource->save($status);
            $status->assignState($state, false, $visibleOnFront);
        } catch (\Exception $e) {
        }
    }
}
