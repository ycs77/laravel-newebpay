<?php

namespace Ycs77\NewebPay\Results;

class MPGTaiwanPayResult extends ResultV1
{
    /**
     * 實際付款金額
     */
    public function payAmt(): int
    {
        return $this->data['PayAmt'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'PayAmt',
        ];
    }
}
