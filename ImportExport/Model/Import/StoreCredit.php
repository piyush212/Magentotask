<?php

namespace Codilar\ImportExport\Model\Import;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\CustomerBalance\Model\BalanceFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Codilar\ImportExport\Model\Source\Import\Behavior\Basic;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Codilar\ImportExport\Model\Import\Validator\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class StoreCredit extends AbstractEntity
{
    protected $_messageTemplates = [ ValidatorInterface::ERROR_TITLE_IS_EMPTY => 'Email is empty',];
    protected $_permanentAttributes = [Basic::CUSTOMER_EMAIL];
    protected $needColumnCheck = true;
    protected $validColumnNames = [Basic::CUSTOMER_EMAIL, Basic::AMOUNT, Basic::COMMENT];
    protected $logInHistory = true;
    protected $_validators = [];
    /**
     * @var AdapterInterface
     */
    protected $_connection;
    /**
     * @var ResourceConnection
     */
    protected $_resource;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var BalanceFactory
     */
    protected $balanceFactory;
    /**
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $customerBalanceData;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param Data $importData
     * @param Config $config
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param StringUtils $string
     * @param BalanceFactory $balanceFactory
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        Data $importData,
        Config $config,
        ResourceConnection $resource,
        Helper $resourceHelper,
        CustomerRepositoryInterface $customerRepository,
        StringUtils $string,
        BalanceFactory $balanceFactory,
        \Magento\CustomerBalance\Helper\Data $customerBalanceData,
        ProcessingErrorAggregatorInterface $errorAggregator,
        LoggerInterface $logger
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->customerRepository = $customerRepository;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->balanceFactory = $balanceFactory;
        $this->customerBalanceData = $customerBalanceData;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->config = $config;
        $this->logger = $logger;
    }


    /**
     * @return array
     */
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'store_credit';
    }

    /**
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum]))
        {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        $this->saveEntity();
        return true;
    }

    /**
     * @return $this
     */
    protected function saveEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch())
        {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData)
            {
                if (!$this->validateRow($rowData, $rowNum))
                {
                    $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated())
                {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowTitle= $rowData[Basic::CUSTOMER_EMAIL];
                $listTitle[] = $rowTitle;
                $entityList[$rowTitle][] = [
                    Basic::CUSTOMER_EMAIL => $rowData[Basic::CUSTOMER_EMAIL],
                    Basic::AMOUNT => $rowData[Basic::AMOUNT],
                    Basic::COMMENT => $rowData[ Basic::COMMENT],];
            }
            if (Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveStoreCredit($entityList);
            }
        }
        return $this;
    }

    /**
     * @param $entityList
     * @return $this
     */
    protected function saveStoreCredit($entityList)
    {
        try {
            //if store credit module is disabled
            if (!$this->customerBalanceData->isEnabled()) {
                return $this;
            }

            //All the validated rows
            foreach ($entityList as $entities) {
               foreach ($entities as $entity) {
                   if (isset($entity) && !empty($entity['amount'])) {
                       try {
                           $customer = $this->customerRepository->get($entity['customer_email']);
                       } catch (LocalizedException $localizedException) {
                           continue;
                       }
                       $balance = $this->balanceFactory->create()->setCustomer($customer);

                       //To get the total amount
                       $model = $balance->loadByCustomer();
                       $totalAmount = $model->getAmount();

                       if($entity['amount'] < 0) {
                           //make it zero if the amount is greater than existing
                           if(abs($entity['amount']) > $totalAmount) {
                              $balance->setAmountDelta(
                                  -$totalAmount
                               )->setComment($entity['comment'])
//                            $balance->setNotifyByEmail(true, $customer->getStoreId());
                                  ->save();
                           } else {
                               //Revert the amount given in the csv
                               $balance->setAmountDelta(
                                   $entity['amount']
                               )->setComment($entity['comment'])
//                            $balance->setNotifyByEmail(true, $customer->getStoreId());
                                   ->save();
                           }

                       } else {
                           $balance->setWebsiteId($customer->getWebsiteId())
                               ->setAmountDelta($entity['amount'])->setComment($entity['comment']);
//                       $balance->setNotifyByEmail(true, $customer->getStoreId());
                           $balance->save();
                       }
                   }
               }
            }
            return $this;
        } catch (\Exception $exception) {
          $this->logger->info($exception->getMessage());
          return $this;
        }
    }

}
