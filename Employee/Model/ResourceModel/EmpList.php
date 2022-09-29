<?php


namespace Piyush\Employee\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EmpList extends AbstractDb
{
    const MAIN_TABLE = 'employee_data';
    const ID_FIELD_NAME = 'id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}