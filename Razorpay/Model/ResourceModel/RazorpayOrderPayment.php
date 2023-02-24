<?php


namespace Codilar\Razorpay\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RazorpayOrderPayment extends AbstractDb
{
    const TABLE_NAME = 'razorpay_order_payment';
    const ID_FIELD_NAME = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, static::ID_FIELD_NAME);
    }
}
