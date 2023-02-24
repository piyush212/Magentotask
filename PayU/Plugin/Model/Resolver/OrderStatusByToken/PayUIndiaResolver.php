<?php


namespace Codilar\PayU\Plugin\Model\Resolver\OrderStatusByToken;

use Codilar\PayU\Helper\Email as EmailHelper;
use Magento\Sales\Api\Data\OrderInterface;
use NexPWA\PayUIndia\Model\Resolver\OrderStatusByToken\PayUIndiaResolver as Subject;

class PayUIndiaResolver
{
    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * PayUIndiaResolver constructor.
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        EmailHelper $emailHelper
    )
    {
        $this->emailHelper = $emailHelper;
    }

    public function afterResolve(Subject $subject, $result, OrderInterface $order)
    {
        if (is_array($result) && $result['is_success'] === false) {
            $this->emailHelper->sendPaymentFailedEmail($order);
        }
        return $result;
    }
}
