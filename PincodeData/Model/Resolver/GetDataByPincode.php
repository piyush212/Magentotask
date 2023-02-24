<?php


namespace Codilar\PincodeData\Model\Resolver;


use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Codilar\PincodeData\Model\ResourceModel\Pincode\CollectionFactory;

class GetDataByPincode implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * GetDataByPincode constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $pincode = $args['pincode'] ?? null;
        if ($pincode) {
            $model = $this->collectionFactory->create()
                ->addFieldToFilter('pincode', $pincode)
                ->getFirstItem();

            if ($model->getId()) {
                return $model->getData();
            }
        }
        return null;
    }
}
