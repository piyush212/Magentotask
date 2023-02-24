<?php

namespace Codilar\ImportExport\Model\Source;

use Codilar\ImportExport\Model\Source\Import\Behavior\Basic;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;

/**
 * @api
 */
class AttributeCollectionProvider
{

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory $attributeFactory
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function get(): Collection
    {
        if (count($this->collection) === 0) {
            /** @var \Magento\Eav\Model\Entity\Attribute $customerEmailAttribute */
            $customerEmailAttribute = $this->attributeFactory->create();
            $customerEmailAttribute->setId(Basic::CUSTOMER_EMAIL);
            $customerEmailAttribute->setDefaultFrontendLabel(Basic::CUSTOMER_EMAIL);
            $customerEmailAttribute->setAttributeCode(Basic::CUSTOMER_EMAIL);
            $customerEmailAttribute->setBackendType('varchar');
            $this->collection->addItem($customerEmailAttribute);

            $customerEmailAttribute = $this->attributeFactory->create();
            $customerEmailAttribute->setId(Basic::MOBILE);
            $customerEmailAttribute->setDefaultFrontendLabel(Basic::MOBILE);
            $customerEmailAttribute->setAttributeCode(Basic::MOBILE);
            $customerEmailAttribute->setBackendType('varchar');
            $this->collection->addItem($customerEmailAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $balanceAttribute */
            $balanceAttribute = $this->attributeFactory->create();
            $balanceAttribute->setId(Basic::BALANCE);
            $balanceAttribute->setBackendType('varchar');
            $balanceAttribute->setDefaultFrontendLabel(Basic::BALANCE);
            $balanceAttribute->setAttributeCode(Basic::BALANCE);
            $this->collection->addItem($balanceAttribute);
        }

        return $this->collection;
    }
}
