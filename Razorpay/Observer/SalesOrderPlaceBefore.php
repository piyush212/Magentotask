<?php


namespace Codilar\Razorpay\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Codilar\Razorpay\Model\Config;

class SalesOrderPlaceBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * SalesOrderPlaceBefore constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        if ($this->config->isRazorpayPayment($order)) {
            $order->setCanSendNewEmailFlag(false);
        }
    }
}
