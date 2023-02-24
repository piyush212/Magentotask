<?php


namespace Codilar\CmsForm\Block;


use Magento\Framework\View\Element\Template;

class FormDataTable extends Template
{
    protected $_template = 'Codilar_CmsForm::form_data_table.phtml';

    /**
     * @param array $formData
     * @return self
     */
    public function setFormData(array $formData)
    {
        return $this->setData('form_data', $formData);
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        $formData = $this->getData('form_data');
        if (!is_array($formData)) {
            $formData = [];
        }
        return $formData;
    }
}
