<?php


namespace Codilar\Brand\Model\Resolver;


use Codilar\Brand\Model\Resolver\Data\GetFeaturedBrands;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class FeaturedBrands implements ResolverInterface
{
    /**
     * @var GetFeaturedBrands
     */
    private $getFeaturedBrands;

    /**
     * FeaturedBrand constructor.
     * @param GetFeaturedBrands $getFeaturedBrands
     */
    public function __construct(
        GetFeaturedBrands $getFeaturedBrands
    )
    {
        $this->getFeaturedBrands = $getFeaturedBrands;
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
        return $this->getFeaturedBrands->execute();
    }
}
