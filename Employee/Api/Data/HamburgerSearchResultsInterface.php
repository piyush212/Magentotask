<?php

namespace Piyush\Employee\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface HamburgerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Piyush\Employee\Api\Data\HamburgerInterface[]
     */
    public function getItems();

    /**
     * @param \Piyush\Employee\Api\Data\HamburgerInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}