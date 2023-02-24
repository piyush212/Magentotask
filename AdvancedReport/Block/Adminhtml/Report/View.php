<?php


namespace Codilar\AdvancedReport\Block\Adminhtml\Report;


use Codilar\AdvancedReport\Model\AdvancedReportInterface;
use Codilar\AdvancedReport\Model\AdvancedReportPool;
use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class View extends Template
{
    /**
     * @var AdvancedReportPool
     */
    private AdvancedReportPool $advancedReportPool;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param AdvancedReportPool $advancedReportPool
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        AdvancedReportPool $advancedReportPool,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    )
    {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->advancedReportPool = $advancedReportPool;
    }

    public function getReportId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return AdvancedReportInterface
     */
    public function getReport(): AdvancedReportInterface
    {
        $reportId = $this->getReportId();
        return $this->advancedReportPool->getReport($reportId);
    }
}
