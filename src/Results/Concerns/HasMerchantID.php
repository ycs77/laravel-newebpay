<?php

namespace Ycs77\NewebPay\Results\Concerns;

trait HasMerchantID
{
    /**
     * 藍新金流商店代號
     */
    public function merchantID(): string
    {
        return $this->result['MerchantID'];
    }
}
