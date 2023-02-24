<?php


namespace Codilar\Razorpay\Model;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class Config
{

    const METHOD_CODE = 'razorpay';
    const PAYMENT_PENDING_STATE = 'pending_payment';
    const PAYMENT_SUCCESS_STATE = 'processing';
    const PAYMENT_FAILED_STATE = 'canceled';

    /**
     * @var \NexPWA\Pwa\Model\Config
     */
    private $config;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationAcquirer;

    /**
     * Config constructor.
     * @param \NexPWA\Pwa\Model\Config $config
     * @param UrlInterface $url
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     */
    public function __construct(
        \NexPWA\Pwa\Model\Config $config,
        UrlInterface $url,
        CountryInformationAcquirerInterface $countryInformationAcquirer
    )
    {
        $this->config = $config;
        $this->url = $url;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * @return \NexPWA\Pwa\Model\Config
     */
    public function getConfigInstance(): \NexPWA\Pwa\Model\Config
    {
        return $this->config;
    }

    public function getConfigValue(string $field)
    {
        return $this->config->getValue(sprintf('payment/%s/%s', self::METHOD_CODE, $field));
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->getConfigValue('active');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->getConfigValue('title');
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return (string)$this->getConfigValue('key');
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return (string)$this->getConfigValue('secret');
    }

    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return (string)$this->getConfigValue('instructions');
    }

    /**
     * @return string
     */
    public function getOrderPrefix(): string
    {
        return (string)$this->getConfigValue('advanced/order_prefix');
    }

    /**
     * @return string
     */
    public function getPaymentPendingStatus(): string
    {
        return (string)$this->getConfigValue('advanced/payment_pending_status');
    }

    /**
     * @return string
     */
    public function getPaymentSuccessStatus(): string
    {
        return (string)$this->getConfigValue('advanced/payment_success_status');
    }

    /**
     * @return string
     */
    public function getPaymentFailedStatus(): string
    {
        return (string)$this->getConfigValue('advanced/payment_failed_status');
    }

    /**
     * @return int|null
     */
    public function getPendingOrderCleanupTime(): ?int
    {
        $time = intval($this->getConfigValue('advanced/pending_order_cleanup_time'));
        if (!$time) {
            $time = null;
        }
        return $time;
    }

    /**
     * @return string
     */
    public function getWebhookUrl(): string
    {
        return (string)$this->getConfigValue('advanced/webhook_url');
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isRazorpayPayment(\Magento\Sales\Model\Order $order): bool
    {
        return $order->getPayment()->getMethod() === self::METHOD_CODE;
    }
}
