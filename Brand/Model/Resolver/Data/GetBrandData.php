<?php


namespace Codilar\Brand\Model\Resolver\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Model\Template\FilterEmulate;
use NexPWA\GraphQlVarnishCache\Model\VarnishXTagPool;

class GetBrandData
{
    /**
     * @var FilterEmulate
     */
    private $filterEmulate;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var VarnishXTagPool
     */
    private $varnishXTagPool;

    /**
     * GetBrandData constructor.
     * @param FilterEmulate $filterEmulate
     * @param StoreManagerInterface $storeManager
     * @param VarnishXTagPool $varnishXTagPool
     */
    public function __construct(
        FilterEmulate $filterEmulate,
        StoreManagerInterface $storeManager,
        VarnishXTagPool $varnishXTagPool
    )
    {
        $this->filterEmulate = $filterEmulate;
        $this->storeManager = $storeManager;
        $this->varnishXTagPool = $varnishXTagPool;
    }

    /**
     * @param \Mageplaza\Shopbybrand\Model\Brand $brand
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData($brand): array
    {
        $this->varnishXTagPool->addTag('brand_' . $brand->getId());
        return array_merge($brand->getData(), [
            'id' => $brand->getId(),
            'title' => $brand->getPageTitle(),
            'image' => $this->getMediaUrl($brand->getImage()),
            'banner_image' => $this->getMediaUrl($brand->getData('banner_image')),
            'is_featured' => (bool)$brand->getIsFeatured(),
            'brand_story' => $this->renderWysiwyg($brand->getDescription()),
            'deals' => $this->renderWysiwyg($brand->getShortDescription()),
            'model' => $brand
        ]);
    }

    /**
     * @param string|null $value
     * @return string
     */
    protected function renderWysiwyg(?string $value): string
    {
        try {
            return $this->filterEmulate->filterDirective((string)$value);
        } catch (\Exception $e) {
            return sprintf('<!--%s-->', __('Something went wrong'));
        }
    }

    /**
     * @param string|null $path
     * @return string|null
     * @throws NoSuchEntityException
     */
    protected function getMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    }
}
