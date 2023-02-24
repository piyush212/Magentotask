<?php


namespace Codilar\AdvancedReport\Controller\Adminhtml\View;


use Codilar\AdvancedReport\Model\AdvancedReportPool;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Generate extends Action
{
    /**
     * @var AdvancedReportPool
     */
    private AdvancedReportPool $advancedReportPool;

    /**
     * Generate constructor.
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
        $reportId = (string)$this->getRequest()->getParam('id');
        $report = $this->advancedReportPool->getReport($reportId);
        if (!$report) {
            $this->messageManager->addErrorMessage(__('Report does not exist'));
            return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
        }
        $data = $report->execute($this->getRequest()->getParam('parameter') ?? []);
        if (!count($data)) {
            $this->messageManager->addErrorMessage(__('Data empty'));
            return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
        }
        $this->arrayToCsvDownload($data, sprintf('%s.csv', $report->getLabel()));
    }

    protected function arrayToCsvDownload($array, $filename = "export.csv", $delimiter=",")
    {
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen('php://memory', 'w');
        $headers = array_keys(reset($array));
        fputcsv($f, $headers, $delimiter);
        // loop over the input array
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, $delimiter);
        }
        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: text/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        // make php send the generated csv lines to the browser
        fpassthru($f);
    }
}
