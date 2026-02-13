<?php

namespace Ycs77\NewebPay\Options\CreditCard;

use Carbon\Carbon;
use Ycs77\NewebPay\Options\Options;

class ReverseOptions extends Options
{
    public string $merchantId = '';

    public int $amount = 0;

    public ?string $orderNo = null;

    public ?string $tradeNo = null;

    public int $indexType = 1;

    public function toArray()
    {
        return array_filter([
            'MerchantID' => $this->merchantId,
            'PostData_' => array_filter([
                'RespondType' => 'JSON',
                'Version' => '1.0',
                'Amt' => $this->amount,
                'MerchantOrderNo' => $this->orderNo,
                'TradeNo' => $this->tradeNo,
                'IndexType' => $this->indexType,
                'TimeStamp' => Carbon::now()->timestamp,
            ], fn ($value) => ! is_null($value)),
        ], fn ($value) => ! is_null($value));
    }
}
