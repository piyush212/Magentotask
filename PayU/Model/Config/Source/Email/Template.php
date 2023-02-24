<?php


namespace Codilar\PayU\Model\Config\Source\Email;


class Template extends \Magento\Config\Model\Config\Source\Email\Template
{
    public function toOptionArray()
    {
        $this->setData('path', 'payment/payu_india/advanced/payment_failed_email');
        return parent::toOptionArray();
    }
}
