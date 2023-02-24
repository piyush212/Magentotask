<?php


namespace Codilar\Razorpay\Cron;


use Codilar\Razorpay\Model\Config;
use Codilar\Razorpay\Model\LoggerService;
use Codilar\Razorpay\Model\RazorpayService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class ProcessPendingOrders
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var OrderCollectionFactory
     */
    private OrderCollectionFactory $orderCollectionFactory;
    /**
     * @var LoggerService
     */
    private LoggerService $loggerService;
    /**
     * @var RazorpayService
     */
    private RazorpayService $razorpayService;

    /**
     * ProcessPendingOrders constructor.
     * @param Config $config
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param LoggerService $loggerService
     * @param RazorpayService $razorpayService
     */
    public function __construct(
        Config $config,
        OrderCollectionFactory $orderCollectionFactory,
        LoggerService $loggerService,
        RazorpayService $razorpayService
    )
    {
        $this->config = $config;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->loggerService = $loggerService;
        $this->razorpayService = $razorpayService;
    }

    public function execute()
    {
        $timeNow = time();
        $cleanupTimeInSeconds = $this->config->getPendingOrderCleanupTime() * 60;
        $orderThresholdTime = gmdate(
            'Y-m-d H:i:s',
            $timeNow - $cleanupTimeInSeconds
        );
        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', $this->config->getPaymentPendingStatus())
            ->addFieldToFilter('created_at', [ 'lteq' => $orderThresholdTime ]);

        $collection->getSelect()->join(
            ['sop' => $collection->getTable('sales_order_payment')],
            'main_table.entity_id = sop.parent_id',
            []
        )->where('sop.method = ?', Config::METHOD_CODE);

        $this->loggerService->log(__('Pending payment order processing start'));
        $this->loggerService->log(__('Orders older than %1 UTC will be canceled', $orderThresholdTime));

        /** @var \Magento\Sales\Model\Order[] $orders */
        $orders = $collection->getItems();
        foreach ($orders as $order) {
            try {
                $this->loggerService->log(__('Processing order %1', $order->getIncrementId()));
                $this->razorpayService->verifyOrderPayment($order);
            } catch (LocalizedException $localizedException) {
                $this->loggerService->log($localizedException->getMessage());
            }
        }

        $this->loggerService->log('Pending payment order processing complete');
    }
}
