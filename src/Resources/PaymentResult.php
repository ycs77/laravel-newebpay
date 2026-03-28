<?php

namespace Ycs77\NewebPay\Resources;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Callback\Trade\MPGCallbackResult;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Results\Trade\PaymentResult as MPGPaymentResult;

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
            $this->crypto, $this->factory->config()
        ))->result($request);
    }
}
