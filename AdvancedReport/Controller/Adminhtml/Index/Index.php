<?php


namespace Codilar\AdvancedReport\Controller\Adminhtml\Index;


use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{

    const ADMIN_RESOURCE = 'Codilar_AdvancedReport::all';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
