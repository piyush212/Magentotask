<?php

namespace Codilar\SearchReport\Model\ResourceModel\Search;

use Codilar\SearchReport\Model\Search;
use Codilar\SearchReport\Model\ResourceModel\Search as SearchResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Search::class, SearchResourceModel::class);
    }
}
