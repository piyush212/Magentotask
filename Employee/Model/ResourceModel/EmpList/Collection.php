<?php


namespace Piyush\Employee\Model\ResourceModel\EmpList;


use Piyush\Employee\Model\EmpList;
use Piyush\Employee\Model\ResourceModel\EmpList as EmpListResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(EmpList::class, EmpListResourceModel::class);
    }
}