<?php


namespace Codilar\Brand\Model\Resolver;


use Codilar\Brand\Model\Resolver\Data\GetBrandByUrlKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Brand implements ResolverInterface
{
    /**
     * @var GetBrandByUrlKey
     */
    private $getBrandByUrlKey;

    /**
     * Brand constructor.
     * @param GetBrandByUrlKey $getBrandByUrlKey
     */
    public function __construct(
        GetBrandByUrlKey $getBrandByUrlKey
    )
    {
        $this->getBrandByUrlKey = $getBrandByUrlKey;
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
        $identifier = $args['identifier'] ?? null;
        if (!$identifier) {
            throw new GraphQlInputException(__('Identifier is required to fetch brand'));
        }
        try {
            return $this->getBrandByUrlKey->execute($identifier);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__('Brand not found'));
        }
    }
}
