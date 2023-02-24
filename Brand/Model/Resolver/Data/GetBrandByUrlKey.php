<?php


namespace Codilar\Brand\Model\Resolver\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Shopbybrand\Model\ResourceModel\Brand as BrandResource;
use Mageplaza\Shopbybrand\Model\BrandFactory;

class GetBrandByUrlKey
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;
    /**
     * @var BrandResource
     */
    private $brandResource;
    /**
     * @var GetBrandData
     */
    private $getBrandData;

    /**
     *
     * @param BrandFactory $brandFactory
     * @param BrandResource $brandResource
     * @param GetBrandData $getBrandData
     */
    public function __construct(
        BrandFactory $brandFactory,
        BrandResource $brandResource,
        GetBrandData $getBrandData
    )
    {
        $this->brandFactory = $brandFactory;
        $this->brandResource = $brandResource;
        $this->getBrandData = $getBrandData;
    }


    /**
     * @param string $urlKey
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(string $urlKey): array
    {
        $collection = $this->brandFactory->create()->getBrandCollection();
        $collection->addFieldToFilter('url_key', $urlKey);
        $brand = $this->brandFactory->create();
        $this->brandResource->load($brand, $collection->getFirstItem()->getData('brand_id'));
        if (!$brand->getId() || !$brand->getData('is_enabled')) {
            throw NoSuchEntityException::singleField('url_key', $urlKey);
        }
        return $this->getBrandData->getData($brand);
    }
}
