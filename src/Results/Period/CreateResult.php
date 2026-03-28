<?php

namespace Ycs77\NewebPay\Results\Period;

use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class CreateResult extends BaseResult
{
    use Concerns\HasMerchantID;
    use Concerns\HasOrderNo;

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
     * {@inheritDoc}
     */
    public function status(): string
    {
        return $this->data['Period']['Status'];
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return $this->data['Period']['Message'] ?? '';
    }

    /**
     * 委託週期類別
     */
    public function periodType(): PeriodType
    {
        return PeriodType::from($this->result['PeriodType']);
    }

    /**
     * 此委託總授權期數
     */
    public function authTimes(): int
    {
        return $this->result['AuthTimes'];
    }

    /**
     * 委託所有授權日期排程
     */
    public function dateArray(): array
    {
        return explode(',', (string) $this->result['DateArray']);
    }

    /**
     * 委託每期金額
     */
    public function periodAmount(): int
    {
        return $this->result['PeriodAmt'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result['PeriodNo'];
    }

    /**
     * 每期授權時間
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function authTime(): string
    {
        return $this->result['AuthTime'];
    }

    /**
     * 藍新金流交易序號
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function tradeNo(): string
    {
        return $this->result['TradeNo'];
    }

    /**
     * 卡號前六與後四碼
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function cardNo(): string
    {
        return $this->result['CardNo'];
    }

    /**
     * 授權碼
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function authCode(): string
    {
        return $this->result['AuthCode'];
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
        return $this->result['RespondCode'];
    }

    /**
     * 款項保管銀行
     *
     * 交易模式為 **立即執行十元授權** 或 **立即執行委託金額授權** 時回傳的參數。
     */
    public function escrowBank(): ?string
    {
        return $this->result['EscrowBank'] ?? null;
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
        return $this->result['AuthBank'];
    }

    /**
     * 收單金融機構中文名稱
     */
    public function authBankName(): string
    {
        $authBank = $this->result['AuthBank'];

        return $this->authBanks[$authBank] ?? $authBank;
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
        return $this->result['PaymentMethod'];
    }

    /**
     * 交易類別中文名稱
     */
    public function paymentMethodName(): string
    {
        $paymentMethod = $this->result['PaymentMethod'];

        return $this->paymentMethods[$paymentMethod] ?? $paymentMethod;
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['Period']['Result'] ?? [];
    }
}
