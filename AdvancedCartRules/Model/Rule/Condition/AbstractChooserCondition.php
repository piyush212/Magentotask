<?php


namespace Codilar\AdvancedCartRules\Model\Rule\Condition;


abstract class AbstractChooserCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';
        $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
        $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
            $image .
            '" alt="" class="v-middle rule-chooser-trigger" title="' .
            __(
                'Open Chooser'
            ) . '" /></a>';
        return $html;
    }

    public function getExplicitApply()
    {
        return true;
    }

    abstract protected function getValueElementChooserUrl();
}
