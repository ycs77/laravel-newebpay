<?php

namespace Ycs77\NewebPay\Results;

class MPGResult extends Result
{
    /**
     * 交易狀態
     *
     * 1. 若交易付款成功，則回傳 SUCCESS。
     * 2. 若交易付款失敗，則回傳錯誤代碼。
     */
    public function status(): string
    {
        return $this->data['Status'];
    }

    /**
     * 交易成功
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'SUCCESS';
    }

    /**
     * 交易失敗
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
     * 回傳格式
     *
     * JSON 格式
     */
    public function respondType(): string
    {
        return $this->result()['RespondType'];
    }

    /**
     * 支付完成時間
     *
     * 當使用超商取貨服務時，本欄位的值會以空值回傳
     */
    public function payTime(): ?string
    {
        return $this->result()['PayTime'] ?? null;
    }

    /**
     * 交易 IP
     */
    public function ip(): string
    {
        return $this->result()['IP'];
    }

    /**
     * 款項保管銀行
     *
     * 如商店是直接與收單機構簽約的閘道模式如：支付寶-玉山銀行、ezPay 電子錢包、LINE Pay，
     * 當使用信用卡支付時，本欄位的值會以空值回傳。
     */
    public function escrowBank(): ?string
    {
        return $this->result()['EscrowBank'] ?? null;
    }

    /**
     * 信用卡支付回傳（一次付清、Google Pay、Samaung Pay、國民旅遊卡、銀聯）
     */
    public function credit(): MPGCreditResult
    {
        return new MPGCreditResult($this->result());
    }

    /**
     * WEBATM、ATM 繳費回傳
     */
    public function atm(): MPGATMResult
    {
        return new MPGATMResult($this->result());
    }

    /**
     * 超商代碼繳費回傳
     */
    public function storeCode(): MPGStoreCodeResult
    {
        return new MPGStoreCodeResult($this->result());
    }

    /**
     * 超商條碼繳費回傳
     */
    public function storeBarcode(): MPGStoreBarcodeResult
    {
        return new MPGStoreBarcodeResult($this->result());
    }

    /**
     * 超商物流回傳
     */
    public function lgs(): MPGLgsResult
    {
        return new MPGLgsResult($this->result());
    }

    /**
     * 跨境支付回傳 (包含簡單付電子錢包、簡單付微信支付、簡單付支付寶)
     */
    public function crossBorder(): MPGCrossBorderResult
    {
        return new MPGCrossBorderResult($this->result());
    }

    /**
     * 玉山 Wallet 回傳
     */
    public function esunWallet(): MPGEsunWalletResult
    {
        return new MPGEsunWalletResult($this->result());
    }

    /**
     * 台灣 Pay 回傳
     */
    public function taiwanPay(): MPGTaiwanPayResult
    {
        return new MPGTaiwanPayResult($this->result());
    }
}
