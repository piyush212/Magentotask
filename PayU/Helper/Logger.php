<?php


namespace Codilar\PayU\Helper;


use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Logger constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function log($message)
    {
        $this->logger->info($message . "\n");
    }
}
