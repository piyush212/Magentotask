<?php
namespace Codilar\SearchReport\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Codilar\SearchReport\Api\Data\SearchInterface;
use Codilar\SearchReport\Model\ResourceModel\Search as ResourceModel;

class Search extends AbstractModel implements
    SearchInterface,
    IdentityInterface
{
    const CACHE_TAG = 'search_data';

    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function setQuery($query)
    {
        return $this->setData('query', $query);
    }

    public function setProduct($numberOfProduct)
    {
        return $this->setData('number_of_product', $numberOfProduct);
    }
}
