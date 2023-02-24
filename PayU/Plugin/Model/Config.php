<?php

namespace Codilar\PayU\Plugin\Model;

use NexPWA\PayUIndia\Model\Config as Subject;

class Config
{
    private \NexPWA\Pwa\Model\Config $config;

    /**
     * Config constructor.
     * @param \NexPWA\Pwa\Model\Config $config
     */
    public function __construct(
        \NexPWA\Pwa\Model\Config $config
    )
    {
        $this->config = $config;
    }

    /**
     * @param Subject $subject
     * @param string $result
     * @return string
     */
    public function afterGetReturnUrl(Subject $subject, string $result): string
    {
        $isUtmNoOverrideEnabled = $this->config->getValue('layout/general/utm_nooverride') == 1;
        if ($isUtmNoOverrideEnabled) {
            $query = parse_url($result, PHP_URL_QUERY);
            if ($query) {
                $result .= '&';
            } else {
                $result .= '?';
            }
            $result .= 'utm_nooverride=1';
        }
        return $result;
    }
}
