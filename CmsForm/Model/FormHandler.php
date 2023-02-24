<?php


namespace Codilar\CmsForm\Model;


use Codilar\CmsForm\Block\FormDataTable;
use Codilar\CmsForm\Helper\Email as EmailHelper;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutInterface;

class FormHandler
{

    const DEFAULT_FORM_NAME = 'CMS Form';
    const DEFAULT_SUCCESS_MESSAGE = 'Your response has been captured successfully';
    const DEFAULT_ERROR_MESSAGE = 'Something went wrong. Please try again later';
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;
    /**
     * @var State
     */
    private State $state;
    /**
     * @var LayoutInterface
     */
    private LayoutInterface $layout;

    /**
     * FormHandler constructor.
     * @param Config $config
     * @param EmailHelper $emailHelper
     * @param State $state
     * @param LayoutInterface $layout
     */
    public function __construct(
        Config $config,
        EmailHelper $emailHelper,
        State $state,
        LayoutInterface $layout
    )
    {
        $this->config = $config;
        $this->emailHelper = $emailHelper;
        $this->state = $state;
        $this->layout = $layout;
    }

    /**
     * @param string $name
     * @param array $data
     * @param string $successMessage
     * @param string $errorMessage
     * @return array
     */
    public function handleFormSubmit(
        $name = self::DEFAULT_FORM_NAME,
        $data = [],
        $successMessage = self::DEFAULT_SUCCESS_MESSAGE,
        $errorMessage = self::DEFAULT_ERROR_MESSAGE
    )
    {
        try {
            if (!$this->config->getIsEnabled()) {
                throw new LocalizedException(__('CMS form is not enabled'));
            }

            try {
                $this->state->getAreaCode();
            } catch (LocalizedException $localizedException) {
                $this->state->setAreaCode(Area::AREA_ADMINHTML);
            }

            /** @var FormDataTable $formDataTableBlock */
            $formDataTableBlock = $this->layout->createBlock(FormDataTable::class);
            $formDataTableBlock->setFormData($data);

            $emailVariables = [
                'form_name' => $name,
                'form_data' => $formDataTableBlock->toHtml()
            ];

            $this->emailHelper->sendEmail(
                $this->config->getEmailTemplate(),
                $emailVariables,
                $this->config->getRecipients()
            );

            return [
                'status' => true,
                'message' => $successMessage
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $errorMessage
            ];
        }
    }
}
