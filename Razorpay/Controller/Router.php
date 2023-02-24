<?php


namespace Codilar\Razorpay\Controller;

use Codilar\Razorpay\Model\Config;
use Magento\Framework\App\RequestInterface;
use Codilar\Razorpay\Controller\WebhookFactory;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var Config
     */
    private Config $config;
    private \Codilar\Razorpay\Controller\WebhookFactory $webhookFactory;

    /**
     * Router constructor.
     * @param Config $config
     * @param \Codilar\Razorpay\Controller\WebhookFactory $webhookFactory
     */
    public function __construct(
        Config $config,
        WebhookFactory $webhookFactory
    )
    {
        $this->config = $config;
        $this->webhookFactory = $webhookFactory;
    }

    /**
     * @inheritDoc
     */
    public function match(RequestInterface $request)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $configPath = $this->config->getWebhookUrl();
            $requestPath = trim($request->getPathInfo(), '/');
            if ($requestPath === $configPath) {
                return $this->webhookFactory->create();
            }
        }
        return null;
    }
}
