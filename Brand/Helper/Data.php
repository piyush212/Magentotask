<?php


namespace Codilar\Brand\Helper;


class Data
{

    private $brandAttributeCode = null;
    /**
     * @var \Mageplaza\Shopbybrand\Helper\Data
     */
    private $brandHelper;

    /**
     * Data constructor.
     * @param \Mageplaza\Shopbybrand\Helper\Data $brandHelper
     */
    public function __construct(
        \Mageplaza\Shopbybrand\Helper\Data $brandHelper
    )
    {
        $this->brandHelper = $brandHelper;
    }

    public function getBrandAttributeCode()
    {
        if (!$this->brandAttributeCode) {
            $this->brandAttributeCode = $this->brandHelper->getAttributeCode();
        }
        return $this->brandAttributeCode;
    }
}
