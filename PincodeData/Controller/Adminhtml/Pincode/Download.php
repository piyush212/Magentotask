<?php


namespace Codilar\PincodeData\Controller\Adminhtml\Pincode;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Codilar\PincodeData\Model\ResourceModel\Pincode\CollectionFactory;

class Download extends Action
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Download constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $fields = ['pincode', 'city', 'state', 'country'];
        $collection = $this->collectionFactory->create()
            ->addFieldToSelect($fields);
        $data = [];

        $data[] = implode(',', $fields);

        /** @var \Codilar\WareIQ\Model\ProductServiceablePincode $item */
        foreach ($collection as $item) {
            $row = [];
            if (!$item->getData('country')) {
                $item->setData('country', 'IN');
            }
            foreach ($fields as $field) {
                $row[] = $item->getData($field);
            }
            $data[] = implode(',', $row);
        }
        header ("Content-Type: application/octet-stream");
        header ("Content-disposition: attachment; filename=pincodes.csv");
        echo implode("\n", $data);
        exit(0);
    }
}
