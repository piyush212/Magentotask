<?php

namespace Codilar\SearchReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Search extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('search_data', 'id');
    }
}
