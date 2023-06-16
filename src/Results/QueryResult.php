<?php

namespace Ycs77\NewebPay\Results;

class QueryResult extends Result
{
    use Concerns\HasVerifyCheckCode;

    /**
     * The newebpay HashKey.
     */
    protected string $hashKey;

    /**
     * The newebpay HashIV.
     */
    protected string $hashIV;

    public function __construct(array $data, string $hashKey, string $hashIV)
    {
        $this->data = $this->transformData($data);
        $this->hashKey = $hashKey;
        $this->hashIV = $hashIV;
    }

    /**
     * 查詢狀態
     *
     * 1. 若查詢成功，則回傳 SUCCESS。
     * 2. 若查詢失敗，則回傳錯誤代碼。
     */
    public function status(): string
    {
        return $this->data['Status'];
    }

    /**
     * 查詢成功
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'SUCCESS';
    }

    /**
     * 查詢失敗
     */
    public function isFail(): bool
    {
        return $this->status() !== 'SUCCESS';
    }

    /**
     * 敘述此次查詢狀態
     */
    public function message(): string
    {
        return $this->data['Message'];
    }

    /**
     * 回傳參數
     */
    public function result(): array
    {
        return $this->data['Result'] ?? [];
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
     * 支付狀態
     *
     * * **0**: 未付款
     * * **1**: 付款成功
     * * **2**: 付款失敗
     * * **3**: 取消付款
     * * **6**: 退款
     */
    public function tradeStatus(): string
    {
        return $this->result()['TradeStatus'];
    }

    /**
     * 支付方式
     *
     * * **CREDIT**: 信用卡付款
     * * **VACC**: 銀行 ATM 轉帳付款
     * * **WEBATM**: 網路銀行轉帳付款
     * * **BARCODE**: 超商條碼繳費
     * * **CVS**: 超商代碼繳費
     * * **LINEPAY**: LINE Pay 付款
     * * **ESUNWALLET**: 玉山 Wallet
     * * **TAIWANPAY**: 台灣 Pay
     * * **CVSCOM**: 超商取貨付款
     */
    public function paymentType(): string
    {
        return $this->result()['PaymentType'];
    }

    /**
     * 交易建立時間
     */
    public function createTime(): string
    {
        return $this->result()['CreateTime'];
    }

    /**
     * 支付完成時間
     */
    public function payTime(): string
    {
        return $this->result()['PayTime'];
    }

    /**
     * 檢核碼
     */
    public function checkCode()
    {
        return $this->result()['CheckCode'];
    }

    /**
     * 驗證資料有沒有被竄改
     */
    public function verify(): bool
    {
        return $this->verifyCheckCode($this->checkCode(), [
            'MerchantID' => $this->merchantId(),
            'Amt' => $this->amt(),
            'MerchantOrderNo' => $this->merchantOrderNo(),
            'TradeNo' => $this->tradeNo(),
        ], $this->hashKey, $this->hashIV);
    }

    /**
     * 預計撥款日
     */
    public function fundTime(): string
    {
        return $this->result()['FundTime'];
    }

    /**
     * 實際交易商店代號
     */
    public function shopMerchantId(): ?string
    {
        return $this->result()['ShopMerchantID'] ?? null;
    }

    /**
     * 信用卡交易回傳（國外卡、國旅卡、ApplePay、GooglePay、SamsungPay）
     */
    public function credit(): QueryCreditResult
    {
        return new QueryCreditResult($this->result());
    }

    /**
     * 超商代碼、超商條碼、超商取貨付款、LINE Pay、ATM、WebATM 回傳
     */
    public function paymentStatus(): QueryPaymentStatusResult
    {
        return new QueryPaymentStatusResult($this->result());
    }

    /**
     * 超商取貨付款回傳
     */
    public function lgs(): QueryLgsResult
    {
        return new QueryLgsResult($this->result());
    }

    /**
     * 電子錢包（LINE Pay、玉山 Wallet、台灣 Pay）
     */
    public function digitalWallet(): QueryDigitalWalletResult
    {
        return new QueryDigitalWalletResult($this->result());
    }
}
