<?php


namespace Codilar\AdvancedReport\Controller\Adminhtml\View;


use Codilar\AdvancedReport\Model\AdvancedReportPool;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Index extends Action
{
    /**
     * @var AdvancedReportPool
     */
    private AdvancedReportPool $advancedReportPool;

    /**
     * Index constructor.
     * @param Context $context
     * @param AdvancedReportPool $advancedReportPool
     */
    public function __construct(
        Context $context,
        AdvancedReportPool $advancedReportPool
    )
    {
        parent::__construct($context);
        $this->advancedReportPool = $advancedReportPool;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $reportId = (string)$this->getRequest()->getParam('id');
        $report = $this->advancedReportPool->getReport($reportId);
        if (!$report) {
            $this->messageManager->addErrorMessage(__('Report does not exist'));
            return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
        }
        $page->getConfig()->getTitle()->set(__('Report "%1"', $report->getLabel()));
        return $page;
    }
}
