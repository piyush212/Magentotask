<?php

namespace Codilar\AdvancedCartRules\Controller\Adminhtml\Chooser;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;

class CartRules extends Action
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents($this->getChooserHtml());
        return $result;
    }

    /**
     * @return string
     */
    protected function getChooserHtml()
    {
        $block = $this->_view->getLayout()->createBlock(
            \Codilar\AdvancedCartRules\Block\Adminhtml\Widget\Chooser\CartRules::class,
            'sales_rule_chooser',
            [
                'data' => [
                    'js_form_object' => $this->getRequest()->getParam('form')
                ]
            ]
        );
        return $block->toHtml();
    }
}
