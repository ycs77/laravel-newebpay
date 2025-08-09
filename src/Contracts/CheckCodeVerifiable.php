<?php

namespace Ycs77\NewebPay\Contracts;

interface CheckCodeVerifiable
{
    /**
     * 商店代號
     */
    public function merchantID(): string;

    /**
     * 商店自訂訂單編號
     */
    public function orderNo(): string;

    /**
     * 交易金額
     */
    public function amount(): int|float;

    /**
     * 訂單金額
     */
    public function tradeNo(): int|float;

    /**
     * 檢查碼
     */
    public function checkCode(): string;
}
