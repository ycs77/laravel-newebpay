<?php

namespace Ycs77\NewebPay\Results\Period;

use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Results\BaseResult;

class AlterStatusResult extends BaseResult
{
    /**
     * {@inheritDoc}
     */
    public function status(): string
    {
        return $this->data['period']['Status'];
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return $this->data['period']['Message'] ?? '';
    }

    /**
     * 商店訂單編號
     */
    public function orderNo(): string
    {
        return $this->result['MerOrderNo'];
    }

    /**
     * 委託單號
     */
    public function periodNo(): string
    {
        return $this->result['PeriodNo'];
    }

    /**
     * 委託狀態
     */
    public function periodStatus(): PeriodStatus
    {
        return PeriodStatus::from($this->result['AlterType']);
    }

    /**
     * 委託下一次授權日期
     */
    public function newNextTime(): string
    {
        return $this->result['NewNextTime'];
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['period']['Result'] ?? [];
    }
}
