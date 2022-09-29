<?php

namespace Piyush\Employee\Controller\EmpList;

use Piyush\Employee\Model\EmpList;
use Piyush\Employee\Model\ResourceModel\EmpList as EmpListResourceModel;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Save extends Action
{
    /**
     * @var EmpList
     */
    private $emplist;
    /**
     * @var EmpListResourceModel
     */
    private $emplistResourceModel;

    /**
     * Add constructor.
     * @param Context $context
     * @param EmpList $emplist
     * @param EmpListResourceModel $emplistResourceModel
     */
    public function __construct(
        Context $context,
        EmpList $emplist,
        EmpListResourceModel $emplistResourceModel
    ) {
        $this->emplist = $emplist;
        $this->emplistResourceModel = $emplistResourceModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        // var_dump($params);
        // die;
        $emplist = $this->emplist->setData($params);//TODO: Challenge Modify here to support the edit save functionality
        try {
            $this->emplistResourceModel->save($emplist);
            $this->messageManager->addSuccessMessage(__("Successfully added the Employee %1", $params['Name']));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("Something went wrong."));
        }
        /* Redirect back to hero display page */
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('employee');
        return $redirect;
    }
}