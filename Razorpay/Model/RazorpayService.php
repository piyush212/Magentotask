<?php


namespace Codilar\Razorpay\Model;

use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Razorpay\Api\Api as RazorpayApi;
use Codilar\Razorpay\Model\RazorpayOrderFactory as RazorpayOrderModelFactory;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrder as RazorpayOrderResource;
use Codilar\Razorpay\Model\RazorpayOrderPaymentFactory as RazorpayOrderPaymentModelFactory;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrderPayment as RazorpayOrderPaymentResource;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;

class RazorpayService
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var LoggerService
     */
    private LoggerService $loggerService;
    /**
     * @var RazorpayOrderFactory
     */
    private RazorpayOrderFactory $razorpayOrderModelFactory;
    /**
     * @var RazorpayOrderResource
     */
    private RazorpayOrderResource $razorpayOrderResource;
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;
    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository;
    /**
     * @var InvoiceService
     */
    private InvoiceService $invoiceService;
    /**
     * @var TransactionFactory
     */
    private TransactionFactory $transactionFactory;
    /**
     * @var InvoiceSender
     */
    private InvoiceSender $invoiceSender;
    /**
     * @var RazorpayOrderPaymentFactory
     */
    private RazorpayOrderPaymentFactory $razorpayOrderPaymentModelFactory;
    /**
     * @var RazorpayOrderPaymentResource
     */
    private RazorpayOrderPaymentResource $razorpayOrderPaymentResource;
    /**
     * @var OrderSender
     */
    private OrderSender $orderSender;
    /**
     * @var OrderManagementInterface
     */
    private OrderManagementInterface $orderManagement;

    /**
     * RazorpayService constructor.
     * @param Config $config
     * @param LoggerService $loggerService
     * @param RazorpayOrderFactory $razorpayOrderModelFactory
     * @param RazorpayOrderResource $razorpayOrderResource
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository
     * @param InvoiceService $invoiceService
     * @param TransactionFactory $transactionFactory
     * @param InvoiceSender $invoiceSender
     * @param RazorpayOrderPaymentFactory $razorpayOrderPaymentModelFactory
     * @param RazorpayOrderPaymentResource $razorpayOrderPaymentResource
     * @param OrderSender $orderSender
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Config $config,
        LoggerService $loggerService,
        RazorpayOrderModelFactory $razorpayOrderModelFactory,
        RazorpayOrderResource $razorpayOrderResource,
        OrderRepositoryInterface $orderRepository,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        InvoiceSender $invoiceSender,
        RazorpayOrderPaymentModelFactory $razorpayOrderPaymentModelFactory,
        RazorpayOrderPaymentResource $razorpayOrderPaymentResource,
        OrderSender $orderSender,
        OrderManagementInterface $orderManagement
    )
    {
        $this->config = $config;
        $this->loggerService = $loggerService;
        $this->razorpayOrderModelFactory = $razorpayOrderModelFactory;
        $this->razorpayOrderResource = $razorpayOrderResource;
        $this->orderRepository = $orderRepository;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
        $this->razorpayOrderPaymentModelFactory = $razorpayOrderPaymentModelFactory;
        $this->razorpayOrderPaymentResource = $razorpayOrderPaymentResource;
        $this->orderSender = $orderSender;
        $this->orderManagement = $orderManagement;
    }

    /**
     * @return RazorpayApi
     */
    public function getAuthenticatedApiInstance(): RazorpayApi
    {
        $key = $this->config->getKey();
        $secret = $this->config->getSecret();
        return new RazorpayApi($key, $secret);
    }

    protected function getOrderId(Order $order)
    {
        return $this->config->getOrderPrefix() . $order->getIncrementId();
    }

    /**
     * @param string $razorpayOrderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getOrderByRazorpayOrderId(string $razorpayOrderId): OrderInterface
    {
        $model = $this->razorpayOrderModelFactory->create();
        $this->razorpayOrderResource->load($model, $razorpayOrderId, 'razorpay_order_id');
        $orderId = $model->getData('order_id');
        if (!$orderId) {
            throw NoSuchEntityException::singleField('razorpay_order_id', $razorpayOrderId);
        }
        return $this->orderRepository->get($orderId);
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public function getRazorpayOrderId(Order $order): ?string
    {
        $model = $this->razorpayOrderModelFactory->create();
        $this->razorpayOrderResource->load($model, $order->getId(), 'order_id');
        if ($model->getData('razorpay_order_id')) {
            return (string)$model->getData('razorpay_order_id');
        }
        return null;
    }

    /**
     * @param Order $order
     * @param string $razorpayOrderId
     * @return void
     * @throws \Exception
     */
    public function setRazorpayOrderId(Order $order, string $razorpayOrderId): void
    {
        $model = $this->razorpayOrderModelFactory->create();
        $this->razorpayOrderResource->load($model, $order->getId(), 'order_id');
        $model->addData([
            'order_id' => $order->getId(),
            'razorpay_order_id' => $razorpayOrderId
        ]);
        $this->razorpayOrderResource->save($model);
    }

    /**
     * @param Order $order
     * @return string
     * @throws LocalizedException
     */
    public function getOrCreateRazorpayOrder(Order $order): string
    {
        if (!$this->config->isActive()) {
            throw new LocalizedException(__('Razorpay is not enabled'));
        }
        if (!$this->config->isRazorpayPayment($order)) {
            throw new LocalizedException(__('Order not placed using razorpay'));
        }
        if ($order->getState() !== Config::PAYMENT_PENDING_STATE) {
            throw new LocalizedException(__('Order is already processed'));
        }

        $razorpayOrderId = $this->getRazorpayOrderId($order);
        if (!$razorpayOrderId) {
            $orderData = [
                'receipt'         => $this->getOrderId($order),
                'amount'          => $order->getGrandTotal() * 100,
                'currency'        => $order->getOrderCurrencyCode()
            ];
            /** @var \Razorpay\Api\Order $razorpayOrder */
            try {
                $razorpayOrder = $this->getAuthenticatedApiInstance()->order->create($orderData);
                $razorpayOrderId = $razorpayOrder->toArray()['id'] ?? null;
                if (!$razorpayOrderId) {
                    throw new LocalizedException(__('razorpay_order_id not found in response %1', \json_encode($orderData)));
                }
                $this->loggerService->log([
                    'Order Data' => \json_encode($orderData),
                    'Response' => \json_encode($razorpayOrder->toArray())
                ]);
                $this->setRazorpayOrderId($order, $razorpayOrderId);
            } catch (\Exception $e) {
                $this->loggerService->log([
                    'Order Data' => \json_encode($orderData),
                    'Error' => $e->getMessage()
                ]);
                throw new LocalizedException(__($e->getMessage()));
            }
        }
        return $razorpayOrderId;
    }

    /**
     * @param Order $order
     * @param bool $failPendingOrder
     * @return array
     * @throws LocalizedException
     */
    public function verifyOrderPayment(Order $order, $failPendingOrder = true)
    {
        $razorpayOrderId = $this->getRazorpayOrderId($order);
        if (!$razorpayOrderId) {
            throw new LocalizedException(__('Razorpay order is not created for order #%1', $order->getIncrementId()));
        }
        if ($order->getState() === Config::PAYMENT_PENDING_STATE) {
            /** @var \Razorpay\Api\Order $razorpayOrder */
            $razorpayOrder = $this->getAuthenticatedApiInstance()->order->fetch($razorpayOrderId);
            $payments = $razorpayOrder->payments()->toArray();
            $this->loggerService->log([
                'Order ID' => $order->getId(),
                'Razorpay Order ID' => $razorpayOrderId,
                'Response' => \json_encode($razorpayOrder->payments()->toArray())
            ]);
            $paymentItems = $payments['items'] ?? [];
            foreach ($paymentItems as $payment) {
                $this->capturePaymentForOrder($order, $payment['id'] ?? null, $payment);
            }

            $orderStatus = $razorpayOrder->toArray()['status'] ?? null;

            if ($orderStatus === 'paid') {
                $this->processOrder(
                    $order,
                    Config::PAYMENT_PENDING_STATE,
                    $this->config->getPaymentPendingStatus(),
                    __('Payment successful. Payment ID [%1]', implode(', ', array_column($paymentItems, 'id')))
                );
                $this->generateInvoice($order, $this->config->getPaymentSuccessStatus());
                $this->orderSender->send($order, true);
            } else if ($failPendingOrder) {
                $failureReason = null;
                if (!count($paymentItems)) {
                    $failureReason = __('Payment not attempted');
                } else {
                    $failureReason = [];
                    foreach ($paymentItems as $paymentItem) {
                        $errorCode = $paymentItem['error_code'] ?? null;
                        $errorDescription = $paymentItem['error_description'] ?? null;
                        $failureReason[] = __('%1 [%2]', $errorDescription, $errorCode);
                    }
                    if (!count($failureReason)) {
                        $failureReason = __('Payment failed. Payment ID [%1]', implode(', ', array_column($paymentItems, 'id')));
                    } else {
                        $failureReason = implode('. ', $failureReason);
                    }
                }
                $order->setData(\Codilar\Wingreens\Plugin\Model\Order::WINGREENS_FORCE_CANCELLABLE_KEY, true);
                $this->orderManagement->cancel($order->getId());
                $this->processOrder(
                    $order,
                    Config::PAYMENT_FAILED_STATE,
                    $this->config->getPaymentFailedStatus(),
                    $failureReason
                );
            }
        }
        if ($order->getState() === Config::PAYMENT_SUCCESS_STATE) {
            return [
                'is_success' => true,
                'status' => __('Payment successful')
            ];
        } else {
            return [
                'is_success' => false,
                'status' => __('Payment pending/failed')
            ];
        }
    }

    protected function capturePaymentForOrder(Order $order, string $paymentId, array $paymentData)
    {
        $model = $this->razorpayOrderPaymentModelFactory->create();
        $this->razorpayOrderPaymentResource->load($model, $paymentId, 'payment_id');
        if (!$model->getId()) {
            $model->addData([
                'order_id' => $order->getId(),
                'payment_id' => $paymentId,
                'data_json' => \json_encode($paymentData)
            ]);
            $this->addOrderComment($order, __('Payment attempted with payment ID %1 and status %2', $paymentId, $paymentData['status']), false);
            $this->razorpayOrderPaymentResource->save($model);
        }
    }

    protected function processOrder(
        Order $order,
        string $state,
        string $status,
        ?string $comment = null
    )
    {
        $order->setState($state);
        $order->setStatus($status);
        $this->orderRepository->save($order);
        if ($comment) {
            $this->addOrderComment($order, $comment, false);
        }
    }

    protected function addOrderComment(Order $order, string $comment, bool $isVisibleOnFront = false)
    {
        try {
            $comment = $order->addCommentToStatusHistory($comment, false, $isVisibleOnFront);
            $this->orderStatusHistoryRepository->save($comment);
        } catch (CouldNotSaveException $e) {
        }
    }

    /**
     * @param Order $order
     * @param string $invoiceSuccessStatus
     */
    protected function generateInvoice(Order $order, string $invoiceSuccessStatus)
    {
        if ($order->canInvoice()) {
            try {
                $invoice = $this->invoiceService->prepareInvoice($order);
                if ($invoice->getTotalQty()) {
                    $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
                    $invoice->register();
                    $invoice->pay();
                    $invoice->getOrder()->setCustomerNoteNotify(true);
                    $order->addCommentToStatusHistory('Automatically INVOICED', false);
                    $transactionModel = $this->transactionFactory
                        ->create()
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $transactionModel->save();
                    $this->invoiceSender->send($invoice);
                    $this->processOrder(
                        $order,
                        Config::PAYMENT_SUCCESS_STATE,
                        $invoiceSuccessStatus,
                        __('Status automatically set to "%1" after invoice generation', $invoiceSuccessStatus)
                    );
                }
            } catch (LocalizedException | \Exception $e) {
                $this->loggerService->log([
                    'order' => $order->getIncrementId(),
                    'type' => __('While generating invoice'),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
