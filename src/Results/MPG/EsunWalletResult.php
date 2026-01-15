<?php

namespace Ycs77\NewebPay\Results\MPG;

use Ycs77\NewebPay\Results\Result;

class EsunWalletResult extends Result
{
    /**
     * 實際付款金額
     */
    public function payAmt(): int
    {
        return $this->data['PayAmt'];
    }

    /**
     * 紅利折抵金額
     */
    public function redDisAmt(): ?int
    {
        return $this->data['RedDisAmt'] ?? null;
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'PayAmt',
            'RedDisAmt',
        ];
    }
}
