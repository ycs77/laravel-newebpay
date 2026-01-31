<?php

namespace Ycs77\NewebPay\Contracts;

interface CheckCodeVerifiable
{
    /**
     * 藍新金流商店代號
     */
    public function merchantID(): string;

    /**
     * 商店自訂訂單編號
     */
    public function orderNo(): string;

    /**
     * 交易金額
     */
    public function amount(): int;

    /**
     * 藍新金流交易序號
     */
    public function tradeNo(): string;

    /**
     * 檢查碼
     */
    public function checkCode(): string;
}
