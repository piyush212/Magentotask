<?php


namespace Codilar\Razorpay\Model\Method\Command;


use Codilar\Razorpay\Model\Config;
use Magento\Framework\DataObject;
use Magento\Payment\Gateway\CommandInterface;

class Initialize implements CommandInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Initialize constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $commandSubject)
    {
        /** @var DataObject $stateObject */
        $stateObject = $commandSubject['stateObject'] ?? null;
        if ($stateObject) {
            $stateObject->addData([
                'status' => $this->config->getPaymentPendingStatus(),
                'state' => Config::PAYMENT_PENDING_STATE,
                'is_notified' => false
            ]);
        }
        return null;
    }
}
