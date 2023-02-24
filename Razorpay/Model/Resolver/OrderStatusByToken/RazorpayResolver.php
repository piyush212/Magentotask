<?php


namespace Codilar\Razorpay\Model\Resolver\OrderStatusByToken;


use Codilar\Razorpay\Model\Config;
use Codilar\Razorpay\Model\RazorpayService;
use Magento\Sales\Api\Data\OrderInterface;
use NexPWA\SalesGraphQl\Model\Resolver\OrderStatusByToken\OrderStatusResolverInterface;

class RazorpayResolver implements OrderStatusResolverInterface
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var RazorpayService
     */
    private RazorpayService $razorpayService;

    /**
     * RazorpayResolver constructor.
     * @param Config $config
     * @param RazorpayService $razorpayService
     */
    public function __construct(
        Config $config,
        RazorpayService $razorpayService
    )
    {
        $this->config = $config;
        $this->razorpayService = $razorpayService;
    }

    /**
     * @inheritDoc
     */
    public function resolve(OrderInterface $order): ?array
    {
        /** @var \Magento\Sales\Model\Order $order */
        if (!$this->config->isRazorpayPayment($order)) {
            return null;
        }
        return $this->razorpayService->verifyOrderPayment($order);
    }
}
