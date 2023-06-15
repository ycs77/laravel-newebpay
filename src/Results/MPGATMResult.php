<?php

namespace Ycs77\NewebPay\Results;

class MPGATMResult extends Result
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
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'PayBankCode',
            'PayerAccount5Code',
        ];
    }
}
