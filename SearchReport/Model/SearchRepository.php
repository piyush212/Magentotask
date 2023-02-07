<?php
namespace Codilar\SearchReport\Model;

use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Codilar\SearchReport\Api\SearchRepositoryInterface;
use Codilar\SearchReport\Api\Data\SearchInterface;
use Codilar\SearchReport\Model\ResourceModel\Search as SearchResourceModel;
use Codilar\SearchReport\Model\ResourceModel\Search\CollectionFactory;

class SearchRepository implements SearchRepositoryInterface
{
    protected $objectFactory;
    protected $searchResourceModel;
    protected $collectionFactory;
    protected $searchResultsFactory;

    /**
     * SearchRepository constructor.
     *
     * @param SearchFactory $objectFactory
     * @param SearchResourceModel $objectResourceModel
     * @param CollectionFactory $collectionFactory
     */

    /**
     * @var SearchFactory
     */
    private SearchFactory $searchFactory;

    public function __construct(
        SearchFactory $searchFactory,
        SearchResourceModel $searchResourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->searchFactory = $searchFactory;
        $this->searchResourceModel  = $searchResourceModel;
        $this->collectionFactory    = $collectionFactory;
      }

    public function save(SearchInterface $search)
    {
        $this->searchResourceModel->save($search);
        return $search;
    }

    public function create()
    {
        return $this->searchFactory->create();
    }
}
