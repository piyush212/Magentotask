<?php


namespace Codilar\Logging\Model;

use Monolog\LoggerFactory;
use Magento\Framework\Logger\Handler\BaseFactory as LoggerHandlerFactory;

class Logger
{
    /**
     * @var LoggerFactory
     */
    private LoggerFactory $loggerFactory;
    /**
     * @var LoggerHandlerFactory
     */
    private LoggerHandlerFactory $loggerHandlerFactory;

    /**
     * Logger constructor.
     * @param LoggerFactory $loggerFactory
     * @param LoggerHandlerFactory $loggerHandlerFactory
     */
    public function __construct(
        LoggerFactory $loggerFactory,
        LoggerHandlerFactory $loggerHandlerFactory
    )
    {
        $this->loggerFactory = $loggerFactory;
        $this->loggerHandlerFactory = $loggerHandlerFactory;
    }

    /**
     * @param string $message
     * @param string $filename
     */
    public function log(string $message, string $filename): void
    {
        $filename = 'var/log/verbose_logging/' . trim($filename, '/');
        $handler = $this->loggerHandlerFactory->create([
            'fileName' => $filename
        ]);
        $logger = $this->loggerFactory->create([
            'name' => 'codilar_logger',
            'handlers' => [
                'system' => $handler
            ]
        ]);
        $logger->info("\n" . $message . "\n");
    }

    /**
     * @param array $messageRows
     * @param string $filename
     */
    public function logArray(array $messageRows, string $filename): void
    {
        $message = [];
        foreach ($messageRows as $key => $value) {
            $message[] = sprintf('%s: %s', $key, $value);
        }
        $message = implode("\n", $message);
        $this->log($message, $filename);
    }
}
