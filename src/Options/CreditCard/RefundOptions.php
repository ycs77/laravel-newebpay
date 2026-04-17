<?php

namespace Ycs77\NewebPay\Options\CreditCard;

use Carbon\Carbon;
use Ycs77\NewebPay\Options\Options;

final class RefundOptions extends Options
{
    public string $merchantId = '';

    public int $amount = 0;

    public string $orderNo = '';

    public int $indexType = 1;

    public string $tradeNo = '';

    public bool $reverse = false;

    public function toArray()
    {
        return [
            'MerchantID' => $this->merchantId,
            'PostData_' => array_filter([
                'RespondType' => 'JSON',
                'Version' => '1.1',
                'Amt' => $this->amount,
                'MerchantOrderNo' => $this->orderNo,
                'TimeStamp' => Carbon::now()->timestamp,
                'IndexType' => $this->indexType,
                'TradeNo' => $this->tradeNo,
                'CloseType' => 2,
                'Cancel' => $this->reverse ? 1 : null,
            ], fn ($value) => ! is_null($value)),
        ];
    }
}
