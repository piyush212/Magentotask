<?php


namespace Codilar\Razorpay\Model\ResourceModel\RazorpayOrderPayment;

use Codilar\Razorpay\Model\RazorpayOrderPayment as Model;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrderPayment as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
