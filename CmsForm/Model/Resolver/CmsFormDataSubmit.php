<?php


namespace Codilar\CmsForm\Model\Resolver;


use Codilar\CmsForm\Model\FormHandler;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CmsFormDataSubmit implements ResolverInterface
{

    /**
     * @var FormHandler
     */
    private FormHandler $formHandler;

    /**
     * CmsFormDataSubmit constructor.
     * @param FormHandler $formHandler
     */
    public function __construct(
        FormHandler $formHandler
    )
    {
        $this->formHandler = $formHandler;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $formName = $args['formName'] ?? FormHandler::DEFAULT_FORM_NAME;
        $formData = $args['formData']['items'] ?? [];
        $successMessage = $args['successMessage'] ?? FormHandler::DEFAULT_SUCCESS_MESSAGE;
        $errorMessage = $args['errorMessage'] ?? FormHandler::DEFAULT_ERROR_MESSAGE;

        return $this->formHandler->handleFormSubmit($formName, $formData, $successMessage, $errorMessage);

    }
}
