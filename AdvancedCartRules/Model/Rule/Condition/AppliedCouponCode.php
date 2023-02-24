<?php


namespace Codilar\AdvancedCartRules\Model\Rule\Condition;


class AppliedCouponCode extends \Magento\Rule\Model\Condition\AbstractCondition
{

    const LABEL = 'Applied coupon code';

    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'applied_coupon_code' => __(self::LABEL)
        ]);
        return $this;
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $model */
        $attributeValue = $model->getQuote()->getCouponCode();
        return $this->validateAttribute($attributeValue);
    }
}
