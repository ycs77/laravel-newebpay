<?php

namespace Ycs77\NewebPay\Results\Trade;

use Ycs77\NewebPay\Results\Result;

class CustomerATMResult extends Result
{
    /**
     * 金融機構代碼
     */
    public function bankCode(): string
    {
        return $this->data['BankCode'];
    }

    /**
     * 繳費代碼
     */
    public function codeNo(): string
    {
        return $this->data['CodeNo'];
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'BankCode',
            'CodeNo',
        ];
    }
}
