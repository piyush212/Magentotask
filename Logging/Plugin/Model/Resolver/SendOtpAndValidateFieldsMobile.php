<?php


namespace Codilar\Logging\Plugin\Model\Resolver;

use Codilar\Logging\Model\Logger;
use Codilar\MobileLogin\Model\Resolver\SendOtpAndValidateFieldsMobile as Subject;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class SendOtpAndValidateFieldsMobile
{

    const EMAIL_EXISTS_FILENAME = 'email_exists_but_number_does_not.log';
    const MOBILE_EXISTS_FILENAME = 'mobile_exists_but_email_does_not.log';

    /**
     * @var Logger
     */
    private Logger $logger;
    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;
    /**
     * @var CustomerResource
     */
    private CustomerResource $customerResource;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    /**
     * @var CustomerCollectionFactory
     */
    private CustomerCollectionFactory $customerCollectionFactory;

    /**
     * SendOtpAndValidateFieldsMobile constructor.
     * @param Logger $logger
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param StoreManagerInterface $storeManager
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        Logger $logger,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory
    )
    {
        $this->logger = $logger;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->storeManager = $storeManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param array ...$args
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundResolve(Subject $subject, callable $proceed, ...$args)
    {
        try {
            $result = $proceed(...$args);
        } catch (\Exception $exception) {
            $apiArgs = $args[4];
            $email = $apiArgs['email'] ?? null;
            $mobile = filter_var($apiArgs['mobile'] ?? '', FILTER_SANITIZE_NUMBER_INT);
            if ($email && $mobile) {
                $this->checkifEmailExists($apiArgs, $email, $mobile);
                $this->checkifMobileExists($apiArgs, $email, $mobile);
            }
            throw $exception;
        }
        return $result;
    }

    protected function checkifEmailExists(array $apiArgs, $email, $mobile)
    {
        $customerModel = $this->customerFactory->create();
        $customerModel->setWebsiteId($this->storeManager->getWebsite()->getId());
        $this->customerResource->loadByEmail($customerModel, $email);
        if ($customerModel->getId() && $customerModel->getData('mobile') !== $mobile) {
            $logData = [
                'message' => 'Email exists but mobile does not',
                'request_obj' => \json_encode($apiArgs)
            ];
            $this->logger->logArray($logData, static::EMAIL_EXISTS_FILENAME);
        }
    }

    protected function checkifMobileExists(array $apiArgs, $email, $mobile)
    {
        $customerModel = $this->customerCollectionFactory->create()
            ->addAttributeToFilter('mobile', $mobile)
            ->getFirstItem();
        if ($customerModel->getId() && $customerModel->getData('email') !== $email) {
            $logData = [
                'message' => 'Mobile exists but email does not',
                'request_obj' => \json_encode($apiArgs)
            ];
            $this->logger->logArray($logData, static::MOBILE_EXISTS_FILENAME);
        }
    }

}
