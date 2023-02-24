<?php


namespace Codilar\PincodeData\Model\ResourceModel\Pincode;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Codilar\PincodeData\Model\Pincode as Model;
use Codilar\PincodeData\Model\ResourceModel\Pincode as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
