<?php

namespace Ycs77\NewebPay\Results\Period;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Results\BaseResult;

class AlterResult extends BaseResult
{
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
     * 委託金額
     */
    public function periodAmount(): int
    {
        return $this->result['AlterAmt'];
    }

    /**
     * 週期類別
     */
    public function periodType(): PeriodType
    {
        return PeriodType::from($this->result['PeriodType']);
    }

    /**
     * 交易週期授權時間
     */
    public function periodPoint(): string
    {
        return $this->result['PeriodPoint'];
    }

    /**
     * 委託下一次授權金額
     */
    public function newNextAmount(): int
    {
        return $this->result['NewNextAmt'];
    }

    /**
     * 委託下一次授權日期
     */
    public function newNextTime(): string
    {
        return $this->result['NewNextTime'];
    }

    /**
     * 授權期數
     */
    public function periodTimes(): int
    {
        return $this->result['PeriodTimes'];
    }

    /**
     * 信用卡到期日
     *
     * 格式為月年
     */
    public function creditExpiredAt(): Carbon
    {
        $date = $this->result['Extday'];
        $year = '20'.substr((string) $date, 0, 2);
        $month = substr((string) $date, 2, 2);

        return Carbon::createFromFormat('Y-m', $year.'-'.$month);
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['Period']['Result'] ?? [];
    }
}
