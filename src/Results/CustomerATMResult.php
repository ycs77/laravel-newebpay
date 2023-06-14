<?php

namespace Ycs77\NewebPay\Results;

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
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'BankCode',
            'CodeNo',
        ];
    }
}
