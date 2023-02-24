<?php

namespace Codilar\AdvancedReport\Model\AdvancedReport;

use Codilar\AdvancedReport\Model\AdvancedReport\Parameter\PageNumber;
use Codilar\AdvancedReport\Model\AdvancedReport\Parameter\PageSize;
use Codilar\AdvancedReport\Model\AdvancedReport\Parameter\Table;
use Codilar\AdvancedReport\Model\AdvancedReportInterface;
use Magento\Framework\App\ResourceConnection;

class TableDump implements AdvancedReportInterface
{
    private Table $table;
    private PageSize $pageSize;
    private PageNumber $pageNumber;
    private ResourceConnection $resourceConnection;

    /**
     * @param Table $table
     * @param PageSize $pageSize
     * @param PageNumber $pageNumber
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Table $table,
        PageSize $pageSize,
        PageNumber $pageNumber,
        ResourceConnection $resourceConnection
    )
    {
        $this->table = $table;
        $this->pageSize = $pageSize;
        $this->pageNumber = $pageNumber;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Table dump');
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return __('Dump the data of one table');
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return [
            $this->table,
            $this->pageSize,
            $this->pageNumber
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(array $parameters): array
    {
        $table = $parameters['table'] ?? null;
        $allTables = $this->table->getAllTables();
        if (!$table || !in_array($table, $allTables)) {
            return [];
        }
        $pageSize = $parameters['page_size'] ?? null;
        $pageNumber = $parameters['page_number'] ?? 1;

        $select = $this->resourceConnection->getConnection()->select()
            ->from($table);

        if ($pageSize) {
            $select->limitPage($pageNumber, $pageSize);
        }
        
        return $select->query()->fetchAll();
    }
}
