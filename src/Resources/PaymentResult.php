<?php

namespace Ycs77\NewebPay\Resources;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Callback\MPGCallbackResult;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Results\MPG\PaymentResult as MPGPaymentResult;

final class PaymentResult
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto
    ) {
        //
    }

    public function result(Request $request): MPGPaymentResult
    {
        return (new MPGCallbackResult(
            $this->factory, $this->crypto
        ))->result($request);
    }
}
