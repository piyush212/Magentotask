<?php


namespace Codilar\AdvancedReport\Model;


interface AdvancedReportInterface
{

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return AdvancedReportParameterInterface[]
     */
    public function getParameters(): array;

    /**
     * @param array $parameters
     * @return array
     */
    public function execute(array $parameters): array;
}
