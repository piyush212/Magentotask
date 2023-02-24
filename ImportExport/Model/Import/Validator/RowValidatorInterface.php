<?php

namespace Codilar\ImportExport\Model\Import\Validator;

use Magento\Framework\Validator\ValidatorInterface;

interface RowValidatorInterface extends ValidatorInterface
{
    const ERROR_MESSAGE_IS_EMPTY = 'EmptyMessage';
    const ERROR_TITLE_IS_EMPTY = 'Email is empty';
    public function init($context);
}
