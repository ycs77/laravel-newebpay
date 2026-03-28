<?php

namespace Ycs77\NewebPay\Resources;

use Ycs77\NewebPay\Builders\CreditCard\CaptureBuilder;
use Ycs77\NewebPay\Builders\CreditCard\RefundBuilder;
use Ycs77\NewebPay\Builders\CreditCard\ReverseBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;

final class CreditCard
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto,
        private readonly HttpTransporter $httpTransporter
    ) {
        //
    }

    public function reverse(): ReverseBuilder
    {
        return new ReverseBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->factory->config()
        );
    }

    public function capture(): CaptureBuilder
    {
        return new CaptureBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->factory->config()
        );
    }

    public function refund(): RefundBuilder
    {
        return new RefundBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->factory->config()
        );
    }
}
