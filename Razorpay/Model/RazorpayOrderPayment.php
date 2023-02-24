<?php


namespace Codilar\Razorpay\Model;


use Magento\Framework\Model\AbstractModel;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrderPayment as ResourceModel;

class RazorpayOrderPayment extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
