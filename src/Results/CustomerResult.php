<?php

namespace Ycs77\NewebPay\Results;

class CustomerResult extends Result
{
    /**
     * 取號狀態
     *
     * 1. 若取號付款成功，則回傳 SUCCESS。
     * 2. 若取號付款失敗，則回傳錯誤代碼。
     */
    public function status(): string
    {
        return $this->data['TradeInfo']['Status'];
    }

    /**
     * 取號成功
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'SUCCESS';
    }

    /**
     * 取號失敗
     */
    public function isFail(): bool
    {
        return $this->status() !== 'SUCCESS';
    }

    /**
     * 敘述此次交易狀態
     */
    public function message(): string
    {
        return $this->data['TradeInfo']['Message'];
    }

    /**
     * 回傳參數
     */
    public function result(): array
    {
        return $this->data['TradeInfo']['Result'] ?? [];
    }

    /**
     * 藍新金流商店代號
     */
    public function merchantId(): string
    {
        return $this->result()['MerchantID'];
    }

    /**
     * 交易金額
     */
    public function amt(): int
    {
        return $this->result()['Amt'];
    }

    /**
     * 藍新金流交易序號
     */
    public function tradeNo(): string
    {
        return $this->result()['TradeNo'];
    }

    /**
     * 商店訂單編號
     */
    public function merchantOrderNo(): string
    {
        return $this->result()['MerchantOrderNo'];
    }

    /**
     * 支付方式
     */
    public function paymentType(): string
    {
        return $this->result()['PaymentType'];
    }

    /**
     * 繳費截止日期
     */
    public function expireDate(): string
    {
        return $this->result()['ExpireDate'];
    }

    /**
     * 繳費截止時間
     */
    public function expireTime(): string
    {
        return $this->result()['ExpireTime'];
    }

    /**
     * ATM 繳費回傳
     */
    public function atm(): CustomerATMResult
    {
        return new CustomerATMResult($this->result());
    }

    /**
     * 超商代碼繳費回傳
     */
    public function storeCode(): CustomerStoreCodeResult
    {
        return new CustomerStoreCodeResult($this->result());
    }

    /**
     * 超商條碼繳費回傳
     */
    public function storeBarcode(): CustomerStoreBarcodeResult
    {
        return new CustomerStoreBarcodeResult($this->result());
    }

    /**
     * 超商物流回傳
     */
    public function lgs(): CustomerLgsResult
    {
        return new CustomerLgsResult($this->result());
    }
}
