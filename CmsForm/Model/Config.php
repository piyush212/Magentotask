<?php


namespace Codilar\CmsForm\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->getValue('cms_form/general/is_enabled') == 1;
    }

    /**
     * @return string[]
     */
    public function getRecipients()
    {
        $recipients = $this->getValue('cms_form/general/recipients');
        if ($recipients) {
            return explode(',', $recipients);
        }
        return [];
    }

    /**
     * @return string
     */
    public function getEmailTemplate()
    {
        return $this->getValue('cms_form/general/email_template');
    }

    public function getValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
