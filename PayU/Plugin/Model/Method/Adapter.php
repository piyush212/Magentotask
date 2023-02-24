<?php


namespace Codilar\PayU\Plugin\Model\Method;

use Codilar\PayU\Helper\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Sales\Model\Order;
use NexPWA\PayUIndia\Model\Config;
use NexPWA\PayUIndia\Model\Method\Adapter as Subject;

class Adapter
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CurlFactory
     */
    private $curlFactory;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Adapter constructor.
     * @param Config $config
     * @param CurlFactory $curlFactory
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        CurlFactory $curlFactory,
        Logger $logger
    )
    {
        $this->config = $config;
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
    }

    public function aroundVerifyPayment(Subject $subject, callable $proceed, Order $order)
    {
        $url = $this->config->getPaymentServiceUrl();
        $action = 'verify_payment';
        $txnId = $this->config->getTxnId($order);
        $curl = $this->curlFactory->create();
        $curl->post($url, [
            'key' => $this->config->getKey(),
            'command' => $action,
            'var1' => $txnId,
            'hash' => $this->config->getPaymentServiceHash($order, $action)
        ]);

        $this->logger->log(sprintf("\nOrderId: %s\nResponse: %s\n", $order->getEntityId(), $curl->getBody()));

        $response = \json_decode($curl->getBody(), true);
        $transactionDetails = $response['transaction_details'][$txnId] ?? [];
        if (!count($transactionDetails)) {
            throw new LocalizedException(__('Something went wrong. Please contact the administrator'));
        }
        $status = $transactionDetails['status'] ?? null;
        if ($status !== 'success') {
            $errorMessage = $transactionDetails['error_Message'] ?? null;
            $errorMessage = $errorMessage ?: 'Payment was not successful for the order';
            if (isset($transactionDetails['error_code'])) {
                $errorMessage .= sprintf('(ERRCODE: %s)', $transactionDetails['error_code']);
            }
            throw new LocalizedException(__($errorMessage));
        }
        return $transactionDetails;
    }
}
