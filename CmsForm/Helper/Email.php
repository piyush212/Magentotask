<?php


namespace Codilar\CmsForm\Helper;

use Magento\Framework\Translate\Inline\StateInterface as InlineTranslation;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Escaper;
use Codilar\CmsForm\Model\Config;
use Psr\Log\LoggerInterface;

class Email
{
    /**
     * @var InlineTranslation
     */
    private InlineTranslation $inlineTranslation;
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;
    /**
     * @var Escaper
     */
    private Escaper $escaper;
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Email constructor.
     * @param InlineTranslation $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param Escaper $escaper
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        InlineTranslation $inlineTranslation,
        TransportBuilder $transportBuilder,
        Escaper $escaper,
        Config $config,
        LoggerInterface $logger
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->escaper = $escaper;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function sendEmail($template, $data = [], $recipients = [], $scopeId = null)
    {
        if (count($recipients)) {
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($this->config->getValue('trans_email/ident_general/name')),
                'email' => $this->escaper->escapeHtml($this->config->getValue('trans_email/ident_general/email')),
            ];

            try {
                $transportBuilder = $this->transportBuilder
                    ->setTemplateIdentifier($template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )->setTemplateVars($data)
                    ->setFromByScope($sender, $scopeId);

                foreach ($recipients as $recipient) {
                    $transportBuilder->addTo($recipient);
                }

                $transport = $transportBuilder->getTransport();
                $transport->sendMessage();
            } catch (\Exception $exception) {
                $this->logger->error('Email sending error: ' . $exception->getMessage());
                throw $exception;
            }
            $this->inlineTranslation->resume();
        }
    }
}
