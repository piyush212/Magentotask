<?php


namespace Codilar\Razorpay\Model\ResourceModel\RazorpayOrder;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codilar\Razorpay\Model\RazorpayOrder as Model;
use Codilar\Razorpay\Model\ResourceModel\RazorpayOrder as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
