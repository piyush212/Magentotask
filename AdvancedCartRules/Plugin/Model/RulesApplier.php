<?php


namespace Codilar\AdvancedCartRules\Plugin\Model;

use Codilar\AdvancedCartRules\Model\AppliedSalesRulePool;
use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RulesApplier as Subject;

class RulesApplier
{
    /**
     * @var AppliedSalesRulePool
     */
    private $appliedSalesRulePool;

    /**
     * RulesApplier constructor.
     * @param AppliedSalesRulePool $appliedSalesRulePool
     */
    public function __construct(
        AppliedSalesRulePool $appliedSalesRulePool
    )
    {
        $this->appliedSalesRulePool = $appliedSalesRulePool;
    }

    /**
     * @param Subject $subject
     * @param Address $address
     * @param Rule $rule
     * @return array
     */
    public function beforeAddDiscountDescription(Subject $subject, $address, $rule)
    {
        $this->appliedSalesRulePool->addAppliedSalesRuleId($rule->getId());
        return [$address, $rule];
    }
}
