<?php


namespace Codilar\PayU\Helper;

use Magento\Framework\Translate\Inline\StateInterface as InlineTranslation;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Escaper;
use NexPWA\Pwa\Model\Config;

class Email
{
    /**
     * @var InlineTranslation
     */
    private $inlineTranslation;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Email constructor.
     * @param InlineTranslation $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param Escaper $escaper
     * @param Config $config
     * @param Logger $logger
     * @param UrlInterface $url
     */
    public function __construct(
        InlineTranslation $inlineTranslation,
        TransportBuilder $transportBuilder,
        Escaper $escaper,
        Config $config,
        Logger $logger,
        UrlInterface $url
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->escaper = $escaper;
        $this->config = $config;
        $this->logger = $logger;
        $this->url = $url;
    }

    public function sendPaymentFailedEmail(OrderInterface $order)
    {
        if (!$this->config->getValue('payment/payu_india/advanced/payment_failed_email_enabled') == 1) {
            return false;
        }
        $this->inlineTranslation->suspend();
        $sender = [
            'name' => $this->escaper->escapeHtml($this->config->getValue('trans_email/ident_sales/name')),
            'email' => $this->escaper->escapeHtml($this->config->getValue('trans_email/ident_sales/email')),
        ];

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->config->getValue('payment/payu_india/advanced/payment_failed_email'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )->setTemplateVars($this->getPaymentFailedTemplateVariables($order))
                ->setFromByScope($sender, $order->getStoreId())
                ->addTo($order->getCustomerEmail())
                ->getTransport();

            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->log('Email sending error: ' . $exception->getMessage());
        }
        $this->inlineTranslation->resume();
        return true;
    }

    public function getPaymentFailedTemplateVariables(OrderInterface $order)
    {
        /** @var \Magento\Sales\Model\Order $order */
        return [
            'order' => $order,
            'order_id' => $order->getIncrementId(),
            'customer_email' => $order->getCustomerEmail(),
            'customer_firstname' => $order->getCustomerFirstname(),
            'customer_lastname' => $order->getCustomerLastname(),
            'store_name' => $order->getStore()->getName(),
            'base_url' => $this->url->getBaseUrl()
        ];
    }
}
