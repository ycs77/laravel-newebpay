<?php

namespace Ycs77\NewebPay\Results\Concerns;

trait HasOrderNo
{
    /**
     * 商店自訂訂單編號
     */
    public function orderNo(): string
    {
        return $this->result['MerchantOrderNo'];
    }
}
