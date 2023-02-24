<?php

namespace Codilar\AdvancedCartRules\Block\Adminhtml\Widget\Chooser;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as SalesRuleCollectionFactory;

class CartRules extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * CartRules constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param SalesRuleCollectionFactory $salesRuleCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        SalesRuleCollectionFactory $salesRuleCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('cartRuleChooserGrid_' . $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
        $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
        $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
        $this->setCollection($salesRuleCollectionFactory->create());
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in_cart_rules flag
        if ($column->getId() == 'in_cart_rules') {
            $selected = $this->getSelectedRules();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('rule_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('rule_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_cart_rules',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_cart_rules',
                'values' => $this->getSelectedRules(),
                'align' => 'center',
                'index' => 'rule_id',
                'use_index' => true
            ]
        );

        $this->addColumn(
            'rule_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60px', 'index' => 'rule_id']
        );

        $this->addColumn(
            'name',
            ['header' => __('Name'), 'sortable' => true, 'index' => 'name']
        );

        return parent::_prepareColumns();
    }

    /**
     * @return mixed
     */
    protected function getSelectedRules()
    {
        $rules = $this->getRequest()->getPost('selected', []);
        return $rules;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'advancedcartrules/chooser/cartrules',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }
}
