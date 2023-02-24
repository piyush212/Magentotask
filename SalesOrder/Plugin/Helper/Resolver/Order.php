<?php

namespace Codilar\SalesOrder\Plugin\Helper\Resolver;

use Magento\Sales\Model\Order as SalesOrder;
use NexPWA\SalesGraphQl\Helper\Resolver\Order as Subject;

class Order extends Subject
{
    /**
     * @param Subject $subject
     * @param $result
     * @param SalesOrder $order
     * @return array
     */
    public function afterGetBaseOrderInfo(Subject $subject, $result, SalesOrder $order): array
    {
        $result['estimated_delivery_date'] = $order->getData('order_expected_delivery_date');
        $result['state'] = $order->getState();
        return $result;
    }
}
