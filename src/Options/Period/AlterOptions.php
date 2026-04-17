<?php

namespace Ycs77\NewebPay\Options\Period;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Options\Options;

final class AlterOptions extends Options
{
    public string $merchantId = '';

    public string $orderNo = '';

    public string $periodNo = '';

    public int $alterAmt = 0;

    public ?PeriodType $periodType = null;

    public ?string $periodPoint = null;

    public ?int $periodTimes = null;

    public ?string $extday = null;

    public function toArray()
    {
        return [
            'MerchantID_' => $this->merchantId,
            'PostData_' => array_filter([
                'RespondType' => 'JSON',
                'Version' => '1.1',
                'TimeStamp' => Carbon::now()->timestamp,
                'MerOrderNo' => $this->orderNo,
                'PeriodNo' => $this->periodNo,
                'AlterAmt' => $this->alterAmt,
                'PeriodType' => $this->periodType?->value,
                'PeriodPoint' => $this->periodPoint,
                'PeriodTimes' => $this->periodTimes,
                'Extday' => $this->extday,
            ], fn ($value) => ! is_null($value)),
        ];
    }
}
