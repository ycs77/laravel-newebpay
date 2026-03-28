<?php

namespace Ycs77\NewebPay\Exceptions;

use RuntimeException;

class InvalidCheckCodeException extends RuntimeException
{
    public function __construct(
        protected array $checkCodeData
    ) {
        parent::__construct('驗證檢查碼無效');
    }

    public function getCheckCodeData(): array
    {
        return $this->checkCodeData;
    }
}
