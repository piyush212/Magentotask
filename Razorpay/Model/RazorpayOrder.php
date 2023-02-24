<?php


namespace Codilar\Razorpay\Model;


use Magento\Framework\Model\AbstractModel;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrder as ResourceModel;

class RazorpayOrder extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
