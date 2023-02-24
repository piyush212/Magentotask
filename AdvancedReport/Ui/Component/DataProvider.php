<?php


namespace Codilar\AdvancedReport\Ui\Component;


use Codilar\AdvancedReport\Model\AdvancedReportPool;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var AdvancedReportPool
     */
    private AdvancedReportPool $advancedReportPool;

    /**
     * DataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param AdvancedReportPool $advancedReportPool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        AdvancedReportPool $advancedReportPool,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, 'id', 'id', $meta, $data);
        $this->advancedReportPool = $advancedReportPool;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }

    public function addOrder($field, $direction)
    {
        return null;
    }

    public function setLimit($offset, $size)
    {
        return null;
    }

    public function getData()
    {
        $reports = $this->advancedReportPool->getReports();
        $items = [];
        foreach ($reports as $id => $report) {
            $items[] = [
                'id' => $id,
                'label' => $report->getLabel(),
                'description' => $report->getDescription()
            ];
        }
        return [
            'items' => $items,
            'totalRecords' => count($items)
        ];
    }

    public function count()
    {
        return $this->getData()['totalRecords'] ?? 0;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();
//        echo '<pre>';
//        print_r($meta);
        return $meta;
    }
}
