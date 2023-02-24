<?php


namespace Codilar\Brand\Plugin\Router;

use Codilar\Brand\Model\Resolver\Data\GetBrandByUrlKey;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use ScandiPWA\Router\ValidationManagerInterface as Subject;

class ValidationManager
{

    /**
     * @var GetBrandByUrlKey
     */
    private $getBrandByUrlKey;

    /**
     * ValidationManager constructor.
     * @param GetBrandByUrlKey $getBrandByUrlKey
     */
    public function __construct(
        GetBrandByUrlKey $getBrandByUrlKey
    )
    {
        $this->getBrandByUrlKey = $getBrandByUrlKey;
    }

    public function aroundValidate(Subject $subject, callable $proceed, RequestInterface $request): bool
    {
        $result = $proceed($request);
        if (!$result) {
            $urlPath = trim($request->getPathInfo(), '/');
            try {
                $this->getBrandByUrlKey->execute($urlPath);
                return true;
            } catch (NoSuchEntityException $e) {}
        }
        return $result;
    }
}
