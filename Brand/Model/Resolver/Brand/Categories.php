<?php


namespace Codilar\Brand\Model\Resolver\Brand;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ExtractDataFromCategoryTree;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\CategoryTree;

class Categories implements ResolverInterface
{
    /**
     * @var CategoryTree
     */
    private $categoryTree;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var ExtractDataFromCategoryTree
     */
    private $extractDataFromCategoryTree;

    /**
     * Categories constructor.
     * @param CategoryTree $categoryTree
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ExtractDataFromCategoryTree $extractDataFromCategoryTree
     */
    public function __construct(
        CategoryTree $categoryTree,
        CategoryRepositoryInterface $categoryRepository,
        ExtractDataFromCategoryTree $extractDataFromCategoryTree
    )
    {
        $this->categoryTree = $categoryTree;
        $this->categoryRepository = $categoryRepository;
        $this->extractDataFromCategoryTree = $extractDataFromCategoryTree;
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
        $categoryIds = explode(',', $model->getData('category_ids'));
        $response = [];
        foreach ($categoryIds as $categoryId) {
            $categoryId = trim($categoryId);
            try {
                $category = $this->categoryRepository->get($categoryId);
                $response[] = current($this->extractDataFromCategoryTree->execute(
                    $this->categoryTree->getTree($info, $category->getId())
                ));
            } catch (\Exception $exception) {}
        }
        return $response;
    }
}
