<?php

declare(strict_types=1);

namespace Codilar\ProductAttribute\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

use Magento\Catalog\Model\ProductRepository;

class ProductAttribute implements ResolverInterface
{

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * CustomAttributes constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|Value
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $id =  $product->getEntityId();

       /** @var Product $product */
       $product = $this->productRepository->getById($id);
       if ($product->getTypeId() != Configurable::TYPE_CODE) {
           return [];
       }

       /** @var Configurable $productTypeInstance */
       $productTypeInstance = $product->getTypeInstance();
       $productTypeInstance->setStoreFilter($product->getStoreId(), $product);

       $attributes = $productTypeInstance->getConfigurableAttributes($product);
       $superAttributeList = [];
       foreach($attributes as $_attribute){
           $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();;
           $superAttributeList[$_attribute->getAttributeId()] = $attributeCode;
       }

        $attributesToReturn = [];

        $attributesToReturn[] = [
                    'configurable_type' => $superAttributeList
                ];

                return $attributesToReturn;
            }
        }
