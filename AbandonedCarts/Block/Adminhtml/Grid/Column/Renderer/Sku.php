<?php

namespace Codilar\AbandonedCarts\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Quote\Model\QuoteFactory;

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $quoteFactory;

    public function __construct(
        Context      $context,
        QuoteFactory $quoteFactory,
        array        $data = []
    )
    {
        parent::__construct($context, $data);
        $this->quoteFactory = $quoteFactory;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
            $customerId = $row->getCustomerId();
            $quote = $this->quoteFactory->create()->loadByCustomer($customerId);
            $items = $quote->getAllItems();
             $skus = [];
            foreach ($items as $item) {
                try {
                    $sku = $item->getSku();
                    $skus[] = $sku;
                }catch (\Exception $e) {
                    $skus[] = __('N/A');
            }
}
        return implode(',', $skus);
        }
}
