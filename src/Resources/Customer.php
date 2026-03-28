<?php

namespace Ycs77\NewebPay\Resources;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Callback\Trade\MPGCustomerResult;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Results\Trade\CustomerResult;

final class Customer
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto
    ) {
        //
    }

    public function customer(Request $request): CustomerResult
    {
        return (new MPGCustomerResult(
            $this->factory, $this->crypto
        ))->result($request);
    }
}
