<?php
/**
 * @package     wingreens.
 * @author      codilar
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://www.codilar.com/
 */

namespace Codilar\Search\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Search\Model\QueryFactory as ModelFactory;
use Magento\Search\Model\ResourceModel\Query as ResourceModel;
use Exception;
use Magento\Store\Model\StoreManagerInterface;

class SearchTerms
{
    /**
     * @var ModelFactory
     */
    private ModelFactory $modelFactory;

    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param ModelFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resourceModel,
        StoreManagerInterface $storeManager
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function addSearchTerm($searchTerm)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $model = $this->modelFactory->create()->loadByQueryText($searchTerm);

        if ($model->getId()) {
            $model->saveIncrementalPopularity();
        } else {
            $model->setData([
                'query_text' => $searchTerm,
                'popularity' => 1,
                'is_active' => 1,
                'store_id' => $storeId,
                'num_results' => 1
            ]);

            try {
                $this->resourceModel->save($model);
            } catch (\Exception $e) {
                throw new Exception(__($e->getMessage()));
            }
        }
    }
}
