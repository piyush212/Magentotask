<?php

namespace Codilar\ImportExport\Model\Source\Import\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

class Basic extends AbstractBehavior
{
    const  ONLY_UPDATE = 'update';
    const CUSTOMER_EMAIL = 'customer_email';
    const AMOUNT = 'amount';
    const COMMENT = 'comment';
    const BALANCE = 'balance';
    const MOBILE = 'mobile';

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'store_credit';
    }

}
