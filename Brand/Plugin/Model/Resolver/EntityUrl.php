<?php


namespace Codilar\Brand\Plugin\Model\Resolver;

use Codilar\Brand\Model\Resolver\Data\GetBrandByUrlKey;
use Magento\Framework\Exception\NoSuchEntityException;
use ScandiPWA\UrlrewriteGraphQl\Model\Resolver\EntityUrl as Subject;

class EntityUrl
{
    const TYPE_BRAND_PAGE = 'BRAND_PAGE';
    /**
     * @var GetBrandByUrlKey
     */
    private $getBrandByUrlKey;

    /**
     * EntityUrl constructor.
     * @param GetBrandByUrlKey $getBrandByUrlKey
     */
    public function __construct(
        GetBrandByUrlKey $getBrandByUrlKey
    )
    {
        $this->getBrandByUrlKey = $getBrandByUrlKey;
    }

    public function aroundResolve(Subject $subject, callable $proceed, ...$args)
    {
        $result = $proceed(...$args);
        if ($result === null) {
            $url = $args[4]['url'] ?? null;
            try {
                $brand = $this->getBrandByUrlKey->execute($url);
                return [
                    'id' => $brand['id'],
                    'type' => self::TYPE_BRAND_PAGE,
                    'url_key' => $brand['url_key'] ?? null
                ];
            } catch (NoSuchEntityException $e) {

            }
        }
        return $result;
    }
}
