<?php


namespace Codilar\AdvancedReport\Model;


class AdvancedReportPool
{
    /**
     * @var AdvancedReportInterface[]
     */
    private array $reports;

    /**
     * AdvancedReportPool constructor.
     * @param array $reports
     */
    public function __construct(
        array $reports = []
    )
    {
        $this->reports = $reports;
        $this->populateReports();
    }

    protected function populateReports()
    {

    }

    /**
     * @return AdvancedReportInterface[]
     */
    public function getReports(): array
    {
        return $this->reports;
    }

    /**
     * @param string $reportId
     * @return AdvancedReportInterface|null
     */
    public function getReport(string $reportId): ?AdvancedReportInterface
    {
        return $this->reports[$reportId] ?? null;
    }
}
