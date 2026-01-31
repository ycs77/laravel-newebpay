<?php

namespace Ycs77\NewebPay\Results\Concerns;

trait HasTradeNo
{
    /**
     * 藍新金流交易序號
     */
    public function tradeNo(): string
    {
        return $this->result['TradeNo'];
    }
}
