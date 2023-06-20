<?php

namespace Ycs77\NewebPay\Results;

class PeriodNotifyResult extends Result
{
    /**
     * 收單金融機構中英文名稱對照
     */
    protected $authBanks = [
        'Esun' => '玉山銀行',
        'Taishin' => '台新銀行',
        'NCCC' => '聯合信用卡中心',
        'CathayBK' => '國泰世華銀行',
        'CTBC' => '中國信託銀行',
        'UBOT' => '聯邦銀行',
    ];

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
     * 交易是否成功
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'SUCCESS';
    }

    /**
     * 交易是否失敗
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
     * 商店訂單編號
     */
    public function merchantOrderNo(): string
    {
        return $this->result()['MerchantOrderNo'];
    }

    /**
     * 自訂單號
     *
     * 格式為：商店訂單編號_期數
     */
    public function orderNo(): string
    {
        return $this->result()['OrderNo'];
    }

    /**
     * 藍新金流交易序號
     */
    public function tradeNo(): string
    {
        return $this->result()['TradeNo'];
    }

    /**
     * 委託之本期授權時間 (Y-m-d h:i:s)
     */
    public function authDate(): string
    {
        return $this->result()['AuthDate'];
    }

    /**
     * 委託之總授權期數
     */
    public function totalTimes(): int
    {
        return $this->result()['TotalTimes'];
    }

    /**
     * 委託之已授權期數，包含授權失敗期數
     */
    public function alreadyTimes(): int
    {
        return $this->result()['AlreadyTimes'];
    }

    /**
     * 委託單本期授權金額
     */
    public function authAmt(): int
    {
        return $this->result()['AuthAmt'];
    }

    /**
     * 授權碼
     */
    public function authCode(): string
    {
        return $this->result()['AuthCode'];
    }

    /**
     * 款項保管銀行
     *
     * 如商店是直接與銀行簽約的信用卡特約商店，當使用信用卡支付時，本欄位會回傳空值
     *
     * * **HNCB**: 華南銀行
     */
    public function escrowBank(): ?string
    {
        return $this->result()['EscrowBank'] ?? null;
    }

    /**
     * 收單金融機構
     *
     * * **Esun**: 玉山銀行
     * * **Taishin**: 台新銀行
     * * **NCCC**: 聯合信用卡中心
     * * **CathayBK**: 國泰世華銀行
     * * **CTBC**: 中國信託銀行
     * * **UBOT**: 聯邦銀行
     */
    public function authBank(): string
    {
        return $this->result()['AuthBank'];
    }

    /**
     * 收單金融機構中文名稱
     */
    public function authBankName(): string
    {
        return $this->authBanks[$this->result()['AuthBank']] ?? $this->result()['AuthBank'];
    }

    /**
     * 下期委託授權日期 (Y-m-d)
     *
     * 授權當期若為最後一期，則回覆該期日期
     */
    public function nextAuthDate(): string
    {
        return $this->result()['NextAuthDate'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result()['PeriodNo'];
    }

    // TODO: 在初次授權時，notify 有幾個欄位沒有回傳
    // MerchantID
    // MerchantOrderNo
    // // OrderNo
    // TradeNo
    // // AuthDate
    // // TotalTimes
    // // AlreadyTimes
    // // AuthAmt
    // AuthCode
    // EscrowBank
    // AuthBank
    // // NextAuthDate
    // PeriodNo
}
