<?php


namespace Codilar\AdvancedReport\Model;


interface AdvancedReportParameterInterface
{

    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_SELECT = 'select';
    const TYPE_MULTISELECT = 'multiselect';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return array|null
     */
    public function getOptions(): ?array;

    /**
     * @return string|null
     */
    public function getDefaultValue(): ?string;

}
