<?php

namespace Ycs77\NewebPay\Results;

use Ycs77\NewebPay\Enums\PeriodType;

class PeriodAmtResult extends Result
{
    /**
     * 交易狀態
     *
     * 1. 若修改執行成功，則回傳 SUCCESS。
     * 2. 若修改執行失敗，則回傳錯誤代碼。
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
     * 商店訂單編號
     */
    public function merchantOrderNo(): string
    {
        return $this->result()['MerOrderNo'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result()['PeriodNo'];
    }

    /**
     * 委託金額
     */
    public function amt(): int
    {
        return $this->result()['AlterAmt'];
    }

    /**
     * 週期類別
     */
    public function periodType(): PeriodType
    {
        return PeriodType::from($this->result()['PeriodType']);
    }

    /**
     * 交易週期授權時間
     */
    public function periodPoint(): string
    {
        return $this->result()['PeriodPoint'];
    }

    /**
     * 委託下一次授權金額
     */
    public function newNextAmt(): int
    {
        return $this->result()['NewNextAmt'];
    }

    /**
     * 委託下一次授權日期
     */
    public function newNextTime(): string
    {
        return $this->result()['NewNextTime'];
    }

    /**
     * 授權期數
     */
    public function periodTimes(): int
    {
        return $this->result()['PeriodTimes'];
    }

    /**
     * 信用卡到期日
     *
     * 格式為月年
     */
    public function creditExpiredAt(): string
    {
        return $this->result()['Extday'];
    }
}
