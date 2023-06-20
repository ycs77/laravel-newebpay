<?php

namespace Ycs77\NewebPay\Results;

use Ycs77\NewebPay\Enums\PeriodType;

class PeriodResult extends Result
{
    /**
     * 交易類別中英文名稱對照
     */
    protected $paymentMethods = [
        'CREDIT' => '台灣發卡機構核發之信用卡',
        'UNIONPAY' => '銀聯卡',
    ];

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
     * 委託週期類別
     */
    public function periodType(): PeriodType
    {
        return PeriodType::from($this->result()['PeriodType']);
    }

    /**
     * 此委託總授權期數
     */
    public function authTimes(): int
    {
        return $this->result()['AuthTimes'];
    }

    /**
     * 委託所有授權日期排程
     */
    public function dateArray(): array
    {
        return explode(',', $this->result()['DateArray']);
    }

    /**
     * 委託每期金額
     */
    public function periodAmt(): int
    {
        return $this->result()['PeriodAmt'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result()['PeriodNo'];
    }

    /**
     * 每期授權時間
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function authTime(): string
    {
        return $this->result()['AuthTime'];
    }

    /**
     * 藍新金流交易序號
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function tradeNo(): string
    {
        return $this->result()['TradeNo'];
    }

    /**
     * 卡號前六與後四碼
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function cardNo(): string
    {
        return $this->result()['CardNo'];
    }

    /**
     * 授權碼
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function authCode(): string
    {
        return $this->result()['AuthCode'];
    }

    /**
     * 銀行回應碼
     *
     * 00 代表刷卡成功，其餘為刷卡失敗。
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function respondCode(): string
    {
        return $this->result()['RespondCode'];
    }

    /**
     * 刷卡是否成功
     */
    public function creditSuccessfully(): bool
    {
        return $this->respondCode() === '00';
    }

    /**
     * 款項保管銀行
     *
     * 如商店是直接與銀行簽約的信用卡特約商店，當使用信用卡支付時，本欄位會回傳空值
     *
     * * **HNCB**: 華南銀行
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
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
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
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
     * 交易類別
     *
     * * **CREDIT**: 台灣發卡機構核發之信用卡
     * * **UNIONPAY**: 銀聯卡
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function paymentMethod(): string
    {
        return $this->result()['PaymentMethod'];
    }

    /**
     * 交易類別中文名稱
     */
    public function paymentMethodName(): string
    {
        return $this->paymentMethods[$this->result()['PaymentMethod']] ?? $this->result()['PaymentMethod'];
    }
}
