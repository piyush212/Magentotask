<?php


namespace Codilar\PincodeData\Controller\Adminhtml\Pincode;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Codilar\PincodeData\Model\ResourceModel\Pincode as ResourceModel;
use Magento\Framework\Exception\LocalizedException;

class Upload extends Action
{
    /**
     * @var ResourceModel
     */
    private $resourceModel;
    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csv;

    /**
     * Upload constructor.
     * @param Context $context
     * @param ResourceModel $resourceModel
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        Context $context,
        ResourceModel $resourceModel,
        \Magento\Framework\File\Csv $csv
    )
    {
        parent::__construct($context);
        $this->resourceModel = $resourceModel;
        $this->csv = $csv;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $data = $this->getCsvData('uploaded_file');
            $this->validateData($data);
            $this->truncateTable();
            $this->addDataToTable($data);
            $this->messageManager->addSuccessMessage(__('%1 pincode(s) added successfully', count($data)));
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
        }
        $result = $this->resultRedirectFactory->create();
        $result->setRefererOrBaseUrl();
        return $result;
    }

    protected function validateData($rows)
    {
        $fields = ['pincode', 'city', 'state'];
        $fieldsCount = count($fields);
        $isHeadersChecked = false;
        foreach ($rows as $key => $row) {
            if (!$isHeadersChecked) {
                foreach ($fields as $field) {
                    if (!in_array($field, array_keys($row))) {
                        throw new LocalizedException(__('%1 is a required column', $field));
                    }
                }
                $isHeadersChecked = true;
            }
            if (count($row) < $fieldsCount) {
                throw new LocalizedException(__('Data is corrupted on line %1', $key + 1));
            }
        }
    }

    protected function addDataToTable($rows)
    {
        $connection = $this->resourceModel->getConnection();
        $tableName = $this->resourceModel->getMainTable();
        $connection->insertMultiple($tableName, $rows);
    }

    protected function truncateTable()
    {
        $connection = $this->resourceModel->getConnection();
        $tableName = $this->resourceModel->getMainTable();
        $connection->truncateTable($tableName);
    }

    protected function getCsvData($fileId)
    {
        $file = $_FILES[$fileId];
        $fileSize = $file['size'] ?? 0;
        $fileType = $file['type'] ?? null;
        $fileName = $file['tmp_name'];
        if (!$fileSize) {
            throw new LocalizedException(__('No CSV file was uploaded'));
        } else if ($fileType !== 'text/csv') {
            throw new LocalizedException(__('Uploaded file must be a valid CSV file'));
        } else if (!$fileName) {
            throw new LocalizedException(__('File corrupted. Please upload again'));
        }
        try {
            $data = $this->csv->getData($fileName);
            $response = [];
            $headers = null;
            foreach ($data as $row) {
                if ($headers === null) {
                    $headers = $row;
                } else {
                    $response[] = array_combine($headers, $row);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
