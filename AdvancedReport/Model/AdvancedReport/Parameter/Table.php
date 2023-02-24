<?php

namespace Codilar\AdvancedReport\Model\AdvancedReport\Parameter;

use Codilar\AdvancedReport\Model\AdvancedReportParameterInterface;
use Magento\Framework\App\ResourceConnection;

class Table implements AdvancedReportParameterInterface
{
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'table';
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Table');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_SELECT;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): ?array
    {
        $response = [];
        foreach ($this->getAllTables() as $table) {
            $response[] = [
                'label' => $table,
                'value' => $table
            ];
        }
        return $response;
    }

    public function getAllTables(): array
    {
        $tables = $this->resourceConnection->getConnection()->query('SELECT TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY table_name ASC')->fetchAll();
        return array_column($tables, 'table_name');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue(): ?string
    {
        return null;
    }
}
