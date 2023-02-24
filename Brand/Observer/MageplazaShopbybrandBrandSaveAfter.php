<?php

namespace Codilar\Brand\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use NexPWA\GraphQlVarnishCache\Helper\Cache as CacheHelper;

class MageplazaShopbybrandBrandSaveAfter implements ObserverInterface
{
    /**
     * @var CacheHelper
     */
    private $cacheHelper;

    /**
     * @param CacheHelper $cacheHelper
     */
    public function __construct(
        CacheHelper $cacheHelper
    )
    {
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Mageplaza\Shopbybrand\Model\Brand $brand */
        $brand = $observer->getEvent()->getData('object');
        $this->cacheHelper->cleanCacheByTag('brand_' . $brand->getId());
    }
}
