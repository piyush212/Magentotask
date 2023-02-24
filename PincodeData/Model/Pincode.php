<?php


namespace Codilar\PincodeData\Model;


use Magento\Framework\Model\AbstractModel;
use Codilar\PincodeData\Model\ResourceModel\Pincode as ResourceModel;

class Pincode extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
