<?php

namespace Codilar\AdvancedCartRules\Model;


class AppliedSalesRulePool
{
    /**
     * @var int[]
     */
    private $appliedSalesRuleIds;

    /**
     * AppliedSalesRulePool constructor.
     * @param array $appliedSalesRuleIds
     */
    public function __construct(
        array $appliedSalesRuleIds = []
    ) {
        $this->appliedSalesRuleIds = $appliedSalesRuleIds;
    }

    public function addAppliedSalesRuleId($id)
    {
        $this->appliedSalesRuleIds[$id] = $id;
    }

    /**
     * @return int[]
     */
    public function getAppliedSalesRuleIds(): array
    {
        return $this->appliedSalesRuleIds;
    }
}
