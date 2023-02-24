<?php


namespace Codilar\Razorpay\Controller;

use Codilar\Razorpay\Model\LoggerService;
use Codilar\Razorpay\Model\RazorpayService;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\Order;

class Webhook implements ActionInterface, CsrfAwareActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;
    /**
     * @var RazorpayService
     */
    private RazorpayService $razorpayService;
    /**
     * @var LoggerService
     */
    private LoggerService $loggerService;

    /**
     * Webhook constructor.
     * @param JsonFactory $jsonFactory
     * @param RazorpayService $razorpayService
     * @param LoggerService $loggerService
     */
    public function __construct(
        JsonFactory $jsonFactory,
        RazorpayService $razorpayService,
        LoggerService $loggerService
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->razorpayService = $razorpayService;
        $this->loggerService = $loggerService;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $rawRequestBody = \file_get_contents('php://input');
        $requestBody = \json_decode($rawRequestBody, true) ?? null;
        $razorpayOrderId = $requestBody['payload']['payment']['entity']['order_id'] ?? '';
        $response = $this->jsonFactory->create();
        try {
            /** @var Order $order */
            $order = $this->razorpayService->getOrderByRazorpayOrderId($razorpayOrderId);
            $this->razorpayService->verifyOrderPayment($order, false);
            $responseData = [
                'status' => true,
                'message' => __('Success')
            ];
        } catch (\Exception $exception) {
            $response->setHttpResponseCode(500);
            $responseData = [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
        $this->loggerService->log([
            'type' => 'HOOK',
            'data' => $rawRequestBody,
            'response' => \json_encode($responseData)
        ]);
        $response->setData($responseData);
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
