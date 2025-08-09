<?php

namespace Ycs77\NewebPay\Exceptions;

use RuntimeException;

class InvalidCheckCodeException extends RuntimeException
{
    protected array $checkCodeData;

    public function __construct(array $checkCodeData)
    {
        $this->checkCodeData = $checkCodeData;

        parent::__construct('驗證檢查碼無效');
    }

    public function getCheckCodeData(): array
    {
        return $this->checkCodeData;
    }
}
