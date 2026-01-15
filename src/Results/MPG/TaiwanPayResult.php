<?php

namespace Ycs77\NewebPay\Results\MPG;

use Ycs77\NewebPay\Results\Result;

class TaiwanPayResult extends Result
{
    /**
     * 實際付款金額
     */
    public function payAmt(): int
    {
        return $this->data['PayAmt'];
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'PayAmt',
        ];
    }
}
