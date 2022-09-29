<?php


namespace Piyush\Employee\Model;


use Magento\Framework\Model\AbstractModel;

class EmpList extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel\EmpList::class);
    }
}