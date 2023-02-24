<?php


namespace Codilar\Brand\Model\Resolver\Brand;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Products implements ResolverInterface
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Products constructor.
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
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
        /** @var \Mageplaza\Shopbybrand\Model\Brand $model */
        $model = $value['model'] ?? null;
        if (!$model) {
            throw new LocalizedException(__('Model not found'));
        }
        $productIds = explode(',', $model->getData('product_ids'));
        $productCollection = $this->productCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToSelect('*')
            ->getItems();

        $response = [];
        foreach ($productCollection as $item) {
            $product = $item->getData();
            $product['model'] = $item;
            $response[] = $product;
        }
        return $response;
    }
}
