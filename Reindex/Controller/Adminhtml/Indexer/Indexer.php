<?php

namespace Codilar\Reindex\Controller\Adminhtml\Indexer;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Codilar\Reindex\Model\Indexer as IndexerModel;

class Indexer extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Codilar_Reindex::indexer_reindex';

    /**
     * Indexer Model
     *
     * @var IndexerModel
     */
    protected IndexerModel $indexer;

    /**
     * Indexer constructor.
     *
     * @param Context      $context context
     * @param IndexerModel $indexer Indexer
     */
    public function __construct(
        Context $context,
        IndexerModel $indexer
    ) {
        parent::__construct($context);
        $this->indexer = $indexer;
    }

    /**
     * Execute Method To Process
     *
     * @return void
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');

        if ($indexerIds) {
            try {
                if (!is_array($indexerIds)) {
                    $this->indexer->reindexById($indexerIds);
                } else {
                    $this->indexer->reindexAll($indexerIds);
                }
                $this->messageManager->addSuccessMessage(__('index(es) processed.'));
            } catch
            (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Re-indexer process did not start.')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        }

        $this->_redirect('*/*/list');
    }
}
