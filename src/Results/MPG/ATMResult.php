<?php

namespace Ycs77\NewebPay\Results\MPG;

use Ycs77\NewebPay\Results\Result;

class ATMResult extends Result
{
    /**
     * 付款人金融機構代碼
     */
    public function payBankCode(): ?string
    {
        return $this->data['PayBankCode'] ?? null;
    }

    /**
     * 付款人金融機構帳號末五碼
     */
    public function payerAccount5Code(): ?string
    {
        return $this->data['PayerAccount5Code'] ?? null;
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'PayBankCode',
            'PayerAccount5Code',
        ];
    }
}
