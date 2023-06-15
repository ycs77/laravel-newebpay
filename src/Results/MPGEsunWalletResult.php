<?php

namespace Ycs77\NewebPay\Results;

class MPGEsunWalletResult extends Result
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
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'PayAmt',
            'RedDisAmt',
        ];
    }
}
