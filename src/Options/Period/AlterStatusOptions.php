<?php

namespace Ycs77\NewebPay\Options\Period;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Options\Options;

class AlterStatusOptions extends Options
{
    public string $merchantId = '';

    public string $orderNo = '';

    public string $periodNo = '';

    public ?PeriodStatus $alterType = null;

    public function toArray()
    {
        return [
            'MerchantID_' => $this->merchantId,
            'PostData_' => [
                'RespondType' => 'JSON',
                'Version' => '1.0',
                'TimeStamp' => Carbon::now()->timestamp,
                'MerOrderNo' => $this->orderNo,
                'PeriodNo' => $this->periodNo,
                'AlterType' => $this->alterType?->value,
            ],
        ];
    }
}
