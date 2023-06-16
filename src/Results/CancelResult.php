<?php

namespace Ycs77\NewebPay\Results;

class CancelResult extends Result
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
}
