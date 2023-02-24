<?php


namespace Codilar\Razorpay\Model\Resolver;


use Codilar\Razorpay\Model\Config;
use Codilar\Razorpay\Model\RazorpayService;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\OrderRepositoryInterface;
use NexPWA\QuoteGraphQl\Api\OrderTokenRepositoryInterface;

class CreateRazorpayOrder implements ResolverInterface
{
    /**
     * @var RazorpayService
     */
    private RazorpayService $razorpayService;
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var OrderTokenRepositoryInterface
     */
    private OrderTokenRepositoryInterface $orderTokenRepository;

    /**
     * CreateRazorpayOrder constructor.
     * @param RazorpayService $razorpayService
     * @param OrderRepositoryInterface $orderRepository
     * @param Config $config
     * @param OrderTokenRepositoryInterface $orderTokenRepository
     */
    public function __construct(
        RazorpayService $razorpayService,
        OrderRepositoryInterface $orderRepository,
        Config $config,
        OrderTokenRepositoryInterface $orderTokenRepository
    )
    {
        $this->razorpayService = $razorpayService;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->orderTokenRepository = $orderTokenRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $orderToken = $args['orderToken'];
        $orderId = $this->orderTokenRepository->loadByOrderToken($orderToken)->getOrderId();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        try {
            $razorpayOrderId = $this->razorpayService->getOrCreateRazorpayOrder($order);
            $billingAddress = $order->getBillingAddress();
            if (!$billingAddress) {
                $billingAddress = new DataObject();
            }
            return [
                'key' => $this->config->getKey(),
                'order_id' => $razorpayOrderId,
                'prefill' => [
                    'name' => $order->getCustomerName(),
                    'email' => $order->getCustomerEmail(),
                    'contact' => $billingAddress->getTelephone()
                ],
                'theme' => [
                    'color' => $this->config->getConfigInstance()->getValue('style/color_code/primary_base_color')
                ]
            ];
        } catch (LocalizedException $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }
    }
}
