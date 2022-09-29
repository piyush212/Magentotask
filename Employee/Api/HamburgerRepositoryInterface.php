<?php

namespace Piyush\Employee\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Piyush\Employee\Api\Data\HamburgerInterface;

interface HamburgerRepositoryInterface
{
    /**
     * @param int $id
     * @return \Piyush\Employee\Api\Data\HamburgerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param \Piyush\Employee\Api\Data\HamburgerInterface $hamburger
     * @return \Piyush\Employee\Api\Data\HamburgerInterface
     */
    public function save(HamburgerInterface $hamburger);

    /**
     * @param \Piyush\Employee\Api\Data\HamburgerInterface $hamburger
     * @return void
     */
    public function delete(HamburgerInterface $hamburger);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Piyush\Employee\Api\Data\HamburgerSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
     public function deleteById($d);
}