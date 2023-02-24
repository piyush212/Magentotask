<?php


namespace Codilar\PincodeData\Block\System\Config\General;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class PincodeData extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Codilar_PincodeData::system/config/general/pincode_data.phtml';

    /**
     * @var AbstractElement
     */
    protected $element;

    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getElementUniqueId()
    {
        return 'pincode_data';
    }

    /**
     * @return AbstractElement
     */
    public function getElement(): AbstractElement
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getExportUrl(): string
    {
        return $this->getUrl('pincodedata/pincode/download');
    }

    /**
     * @return string
     */
    public function getImportUrl(): string
    {
        return $this->getUrl('pincodedata/pincode/upload');
    }
}
