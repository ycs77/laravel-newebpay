<?php

namespace Ycs77\NewebPay\Results;

use Ycs77\NewebPay\Enums\PeriodStatus;

class PeriodStatusResult extends Result
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
     * 委託狀態
     */
    public function periodStatus(): PeriodStatus
    {
        return PeriodStatus::from($this->result()['AlterType']);
    }

    /**
     * 委託下一次授權日期
     */
    public function newNextTime(): string
    {
        return $this->result()['NewNextTime'];
    }
}
