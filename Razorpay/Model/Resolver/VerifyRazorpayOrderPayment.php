<?php


namespace Codilar\Razorpay\Model\Resolver;


use Codilar\Razorpay\Model\RazorpayService;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use ScandiPWA\QuoteGraphQl\Model\Customer\CheckCustomerAccount;

class VerifyRazorpayOrderPayment implements ResolverInterface
{
    /**
     * @var RazorpayService
     */
    private RazorpayService $razorpayService;
    /**
     * @var CheckCustomerAccount
     */
    private CheckCustomerAccount $checkCustomerAccount;
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * VerifyRazorpayOrderPayment constructor.
     * @param RazorpayService $razorpayService
     * @param CheckCustomerAccount $checkCustomerAccount
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        RazorpayService $razorpayService,
        CheckCustomerAccount $checkCustomerAccount,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->razorpayService = $razorpayService;
        $this->checkCustomerAccount = $checkCustomerAccount;
        $this->orderRepository = $orderRepository;
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
        $orderId = $args['orderId'];
        $customerId = $context->getUserId();
        $this->checkCustomerAccount->execute($customerId, $context->getUserType());
        /** @var Order $order */
        $order = $this->orderRepository->get($orderId);

        if ($customerId != $order->getCustomerId()) {
            throw new GraphQlNoSuchEntityException(__('Order does not belong to customer'));
        }

        try {
            return $this->razorpayService->verifyOrderPayment($order);
        } catch (\Exception $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        }
    }
}
