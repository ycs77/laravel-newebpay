<?php

namespace Ycs77\NewebPay\Options\Trade;

use Ycs77\NewebPay\Options\Options;

class QueryOptions extends Options
{
    public string $merchantId = '';

    public string $version = '1.3';

    public int $timestamp = 0;

    public string $orderNo = '';

    public int $amount = 0;

    public ?string $gateway = null;

    public function toArray()
    {
        $checkValues = [
            'MerchantID' => $this->merchantId,
            'MerchantOrderNo' => $this->orderNo,
            'Amt' => $this->amount,
        ];

        return array_filter([
            'MerchantID' => $this->merchantId,
            'Version' => $this->version,
            'RespondType' => 'JSON',
            'CheckValue' => $checkValues,
            'TimeStamp' => $this->timestamp,
            'MerchantOrderNo' => $this->orderNo,
            'Amt' => $this->amount,
            'Gateway' => $this->gateway,
        ], fn ($value) => ! is_null($value));
    }
}
