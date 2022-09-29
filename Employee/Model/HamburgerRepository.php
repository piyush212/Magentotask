<?php
    namespace Piyush\Employee\Model;

    use \Piyush\Employee\Api\Data\HamburgerInterface;
    use \Piyush\Employee\Model\ResourceModel\EmpList as ObjectResourceModel;
    use \Magento\Framework\Api\SearchCriteriaInterface;
    use \Magento\Framework\Exception\CouldNotSaveException;
    use \Magento\Framework\Exception\NoSuchEntityException;
    use \Magento\Framework\Exception\CouldNotDeleteException;

    class HamburgerRepository implements \Piyush\Employee\Api\HamburgerRepositoryInterface
    {
        protected $objectFactory;

        protected $objectResourceModel;

        protected $collectionFactory;

        protected $searchResultsFactory;

        public function __construct(
            \Piyush\Employee\Model\HamburgerFactory $objectFactory,
            ObjectResourceModel $objectResourceModel,
            \Piyush\Employee\Model\ResourceModel\EmpList\CollectionFactory $collectionFactory,
            \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
        ) {
            $this->objectFactory        = $objectFactory;
            $this->objectResourceModel  = $objectResourceModel;
            $this->collectionFactory    = $collectionFactory;
            $this->searchResultsFactory = $searchResultsFactory;
        }

        public function save(HamburgerInterface $object)
        {
            $Name = $object->getName();
            // $hasSpouse = $object->getSpouse();
            if ($Name == true) {
                $Name = "Mrs. " . $Name;
            } else {
                $Name = "Miss. " . $Name;
            }
            $object->setName($Name);
            try {
                $this->objectResourceModel->save($object);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__($e->getMessage()));
            }
            return $object;
        }

        /**
         * @inheritdoc
         */
        public function getById($id)
        {
            $object = $this->objectFactory->create();
            $this->objectResourceModel->load($object, $id);
            if (!$object->getId()) {
                throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
            }
            return $object;
        }

        public function delete(HamburgerInterface $object)
        {
            try {
                $this->objectResourceModel->delete($object);
            } catch (\Exception $exception) {
                throw new CouldNotDeleteException(__($exception->getMessage()));
            }
            return true;
        }

        public function deleteById($d)
        {
            return $this->delete($this->getById($d));
        }

        /**
         * @inheritdoc
         */
        public function getList(SearchCriteriaInterface $criteria)
        {
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);
            $collection = $this->collectionFactory->create();
            foreach ($criteria->getFilterGroups() as $filterGroup) {
                $fields = [];
                $conditions = [];
                foreach ($filterGroup->getFilters() as $filter) {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
                if ($fields) {
                    $collection->addFieldToFilter($fields, $conditions);
                }
            }
            $searchResults->setTotalCount($collection->getSize());
            $sortOrders = $criteria->getSortOrders();
            if ($sortOrders) {
                /** @var SortOrder $sortOrder */
                foreach ($sortOrders as $sortOrder) {
                    $collection->addOrder(
                        $sortOrder->getField(),
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                    );
                }
            }
            $collection->setCurPage($criteria->getCurrentPage());
            $collection->setPageSize($criteria->getPageSize());
            $objects = [];
            foreach ($collection as $objectModel) {
                $objects[] = $objectModel;
            }
            $searchResults->setItems($objects);
            return $searchResults;
        }
    }