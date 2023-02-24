<?php

namespace Codilar\AdvancedCartRules\Model\Rule\Condition;

use Codilar\AdvancedCartRules\Model\AppliedSalesRulePool;
use Magento\Framework\UrlInterface;
use Magento\Rule\Model\Condition\Context;

class AppliedSalesRule extends AbstractChooserCondition
{
    const LABEL = 'Applied sales rule';
    /**
     * @var AppliedSalesRulePool
     */
    private $appliedSalesRulePool;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * AppliedSalesRule constructor.
     * @param Context $context
     * @param AppliedSalesRulePool $appliedSalesRulePool
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        AppliedSalesRulePool $appliedSalesRulePool,
        UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->appliedSalesRulePool = $appliedSalesRulePool;
        $this->url = $url;
    }

    public function loadAttributeOptions()
    {
        $this->_inputType = 'multiselect';
        $this->setAttributeOption([
            'applied_sales_rule' => __(self::LABEL)
        ]);
        return $this;
    }



    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $model */
        $attributeValue = implode(',', $this->appliedSalesRulePool->getAppliedSalesRuleIds());
        return $this->validateAttribute($attributeValue);
    }

    protected function getValueElementChooserUrl()
    {
        $params = [];
        if ($this->getJsFormObject()) {
            $params['form'] = $this->getJsFormObject();
        }
        return $this->url->getUrl('advancedcartrules/chooser/cartrules', $params);
    }
}
