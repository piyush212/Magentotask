<?php

namespace Codilar\ImportExport\Model\Export;

use Magento\CustomerBalance\Model\Balance;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Export\AbstractEntity;
use Magento\CustomerBalance\Model\ResourceModel\Balance\CollectionFactory as CustomerBalanceCollectionFactory;
use Magento\ImportExport\Model\Export\Factory;
use Codilar\ImportExport\Model\Source\Import\Behavior\Basic;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Codilar\ImportExport\Model\Source\AttributeCollectionProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\CustomerBalance\Model\BalanceFactory;
use Zend\Log\Logger;

class StoreCredit extends AbstractEntity
{
    /**
     * Permanent entity columns
     *
     * @var string[]
     */
    protected $_permanentAttributes = [
        Basic::CUSTOMER_EMAIL,
        Basic::MOBILE,
        Basic::BALANCE
    ];

    /**
     * @var AttributeCollectionProvider
     */
    protected $attributeCollectionProvider;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var BalanceFactory
     */
    private BalanceFactory $balanceFactory;

    private $customerBalanceCollection;

    /**
     * StoreCredit constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Factory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param AttributeCollectionProvider $attributeCollectionProvider
     * @param CustomerBalanceCollectionFactory $customerBalanceCollectionFactory
     * @param LoggerInterface $logger
     * @param BalanceFactory $balanceFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Factory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        CollectionByPagesIteratorFactory $resourceColFactory,
        AttributeCollectionProvider $attributeCollectionProvider,
        CustomerBalanceCollectionFactory $customerBalanceCollectionFactory,
        LoggerInterface $logger,
        BalanceFactory $balanceFactory,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->attributeCollectionProvider = $attributeCollectionProvider;
        $this->balanceFactory = $balanceFactory;
        $this->customerBalanceCollection = $customerBalanceCollectionFactory->create();
        $this->_pageSize = 1000;
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
    }


    /**
     * Entity attributes collection getter
     *
     * @return \Magento\Framework\Data\Collection
     * @throws \Exception
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollectionProvider->get();
    }

    /**
     * @inheritDoc
     */
    public function export()
    {
        $writer = $this->getWriter();

        // create export file
        $writer->setHeaderCols($this->_getHeaderColumns());
        $this->_exportCollectionByPages($this->_getEntityCollection());

        return $writer->getContents();
    }

    /**
     * @param Balance $item
     */
    public function exportItem($item)
    {
        try {
            $customer = $this->customerRepository->getById($item->getCustomerId());
            $row = [
                Basic::CUSTOMER_EMAIL => $customer->getEmail(),
                Basic::MOBILE => $customer->getCustomAttribute('mobile') ? $customer->getCustomAttribute('mobile')->getValue() : '',
                Basic::BALANCE => $item->getAmount()
            ];
            $this->getWriter()->writeRow($row);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return  'store_credit';
    }

    /**
     * @return array|string[]
     */
    protected function _getHeaderColumns()
    {
        return $this->_permanentAttributes;
    }

    protected function _getEntityCollection()
    {
        return $this->customerBalanceCollection;
    }
}
