<?php

namespace Codilar\SearchReport\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface SearchInterface extends ExtensibleDataInterface
{
    public function getId();
    public function setQuery($query);
    public function setProduct($numberOfProduct);

}
