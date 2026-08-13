<?php

namespace Ycs77\NewebPay\Results\Concerns;

use Ycs77\NewebPay\Enums\PaymentType;

trait HasPaymentType
{
    protected function hasPaymentType(PaymentType ...$paymentTypes): bool
    {
        $paymentType = $this->result['PaymentType'] ?? null;

        return is_string($paymentType)
            && in_array(PaymentType::tryFrom($paymentType), $paymentTypes, true);
    }
}
