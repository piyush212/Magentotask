<?php


namespace Codilar\Brand\Model\Resolver\Data;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Shopbybrand\Api\BrandRepositoryInterface;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Mageplaza\Shopbybrand\Model\Brand;

class GetFeaturedBrands
{
    /**
     * @var GetBrandByUrlKey
     */
    private $getBrandByUrlKey;
    /**
     * @var Helper
     */
    private $helper;

    /**
     * GetFeaturedBrands constructor.
     * @param GetBrandByUrlKey $getBrandByUrlKey
     * @param Helper $helper
     */
    public function __construct(
        GetBrandByUrlKey $getBrandByUrlKey,
        Helper $helper
    )
    {
        $this->getBrandByUrlKey = $getBrandByUrlKey;
        $this->helper = $helper;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(): array
    {
        $response = [];
        $brandCollection = $this->helper->getBrandList();
        $brandCollection->addFieldToFilter('is_featured', 1);

        // reset the existing order by
        $brandCollection->getSelect()->reset(\Zend_Db_Select::ORDER);
        // add the new order by sort_order
        $brandCollection->getSelect()->order('main_table.sort_order ASC');

        /** @var Brand $brand */
        foreach ($brandCollection->getItems() as $brand) {
            try {
                $response[] = $this->getBrandByUrlKey->execute($brand->getUrlKey());
            } catch (LocalizedException $exception) {
                continue;
            }
        }
        return $response;
    }
}
