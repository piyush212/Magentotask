<?php

namespace Codilar\AdvancedReport\Model\AdvancedReport\Parameter;

use Codilar\AdvancedReport\Model\AdvancedReportParameterInterface;

class PageNumber implements AdvancedReportParameterInterface
{

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'page_number';
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Page number');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE_TEXT;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue(): ?string
    {
        return 1;
    }
}
