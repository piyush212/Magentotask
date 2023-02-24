<?php


namespace Codilar\PaymentMethodFee\Plugin\Helper\Resolver;

use NexPWA\SalesGraphQl\Helper\Resolver\Order as Subject;

class Order
{
    /**
     * @param Subject $subject
     * @param $result
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    public function afterGetBaseOrderInfo(Subject $subject, $result, $order)
    {
        $result['payment_method_fee'] = $order->getData(\Codilar\PaymentMethodFee\Model\Total\PaymentMethodFee::PAYMENT_METHOD_FEE_KEY);
        $result['payment_method_fee_label'] = $order->getData(\Codilar\PaymentMethodFee\Model\Total\PaymentMethodFee::PAYMENT_METHOD_FEE_LABEL_KEY);
        return $result;
    }
}
