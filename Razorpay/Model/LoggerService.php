<?php


namespace Codilar\Razorpay\Model;


use Psr\Log\LoggerInterface;

class LoggerService
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

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

    /**
     * @param string|array $message
     */
    public function log($message)
    {
        if (is_array($message)) {
            $logString = [];
            foreach ($message as $key => $value) {
                $logString[] = sprintf("%s - %s", $key, $value);
            }
            $message = implode("\n", $logString);
        }
        $this->logger->info("\n" . $message . "\n");
    }
}
