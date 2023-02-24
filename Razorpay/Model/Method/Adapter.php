<?php


namespace Codilar\Razorpay\Model\Method;


class Adapter extends \Magento\Payment\Model\Method\Adapter
{
    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return (string)$this->getConfigData('instructions');
    }
}
