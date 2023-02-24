<?php
namespace Codilar\Reindex\Model;

use Exception;
use Magento\Indexer\Model\IndexerFactory;

class Indexer
{
    /**
     * $indexerFactory
     *
     * @var IndexerFactory
     */
    protected IndexerFactory $indexerFactory;

    /**
     * Indexer constructor.
     *
     * @param IndexerFactory $indexerFactory IndexerFactory
     */
    public function __construct(
        IndexerFactory $indexerFactory
    ) {
        $this->indexerFactory = $indexerFactory;
    }

    /**
     * Reindex indexes by ids
     *
     * @param array | null $indexIds Indexer Ids
     *
     * @return void
     * @throws Exception
     */
    public function reindexAll($indexIds)
    {
        if ($indexIds) {
            foreach ($indexIds as $indexId) {
                $this->reindexById($indexId);
            }
        }
    }

    /**
     * Single indexer reindex
     *
     * @param int $indexerId Indexer Id
     *
     * @return void
     * @throws Exception
     */
    public function reindexById($indexerId)
    {
        $indexer = $this->indexerFactory->create()->load($indexerId);
        if ($indexer && $indexer->getId()) {
            $indexer->reindexAll();
        }
    }
}
