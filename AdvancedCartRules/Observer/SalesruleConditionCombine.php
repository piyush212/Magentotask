<?php

namespace Codilar\AdvancedCartRules\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesruleConditionCombine implements ObserverInterface
{
    /**
     * @var array
     */
    private $conditions;

    /**
     * SalesruleConditionCombine constructor.
     * @param array $conditions
     */
    public function __construct(
        array $conditions = []
    ) {
        $this->conditions = $conditions;
    }

    public function execute(Observer $observer)
    {
        /** @var DataObject $additional */
        $additional = $observer->getEvent()->getData('additional');
        $conditions = (array)$additional->getData('conditions');
        $conditions = array_merge_recursive($conditions, $this->conditions);
        $additional->setData('conditions', $conditions);
    }
}
