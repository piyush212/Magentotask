<?php


namespace Codilar\EmailLog\Plugin\Mail;

use Codilar\EmailLog\Helper\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterface as Subject;

class Transport
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Transport constructor.
     * @param Logger $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Logger $logger,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundSendMessage(Subject $subject, callable $proceed)
    {
        $isLoggingEnabled = $this->scopeConfig->getValue('system/smtp/log') == 1;
        if ($isLoggingEnabled) {
            $message = $this->prepareMessageToLog($subject->getMessage());
            $this->logger->log($message);
        }
        return $proceed();
    }

    /**
     * @param MessageInterface $message
     * @return string
     */
    protected function prepareMessageToLog(MessageInterface $message)
    {
        $body = $message->getBody();
        if ($body instanceof \Laminas\Mime\Message) {
            $body = $body->generateMessage();
        }
        return sprintf(
            "\n\n[SUBJECT]  %s\n\n[BODY]  %s\n\n",
            $message->getSubject(),
            $body
        );
    }
}
