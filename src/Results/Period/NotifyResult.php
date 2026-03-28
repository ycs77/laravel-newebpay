<?php

namespace Ycs77\NewebPay\Results\Period;

use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class NotifyResult extends BaseResult
{
    use Concerns\HasMerchantID;
    use Concerns\HasOrderNo;

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
     * 藍新金流交易序號
     */
    public function tradeNo(): string
    {
        return $this->result['TradeNo'];
    }

    /**
     * 委託之本期授權時間 (Y-m-d h:i:s)
     */
    public function authDate(): string
    {
        return $this->result['AuthDate'];
    }

    /**
     * 委託之總授權期數
     */
    public function totalTimes(): int
    {
        return $this->result['TotalTimes'];
    }

    /**
     * 委託之已授權期數，包含授權失敗期數
     */
    public function alreadyTimes(): int
    {
        return $this->result['AlreadyTimes'];
    }

    /**
     * 委託單本期授權金額
     */
    public function authAmount(): int
    {
        return $this->result['AuthAmt'];
    }

    /**
     * 授權碼
     */
    public function authCode(): string
    {
        return $this->result['AuthCode'];
    }

    /**
     * 款項保管銀行
     */
    public function escrowBank(): ?string
    {
        return $this->result['EscrowBank'] ?? null;
    }

    /**
     * 收單金融機構
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
     * 下期委託授權日期 (Y-m-d)
     *
     * 授權當期若為最後一期，則回覆該期日期
     */
    public function nextAuthDate(): string
    {
        return $this->result['NextAuthDate'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result['PeriodNo'];
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['Period']['Result'] ?? [];
    }
}
