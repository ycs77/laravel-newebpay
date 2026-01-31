<?php

namespace Ycs77\NewebPay\Resources;

use Ycs77\NewebPay\Builders\Trade\MPGBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;

/**
 * @mixin \Ycs77\NewebPay\Builders\Trade\MPGBuilder
 */
final class Payment
{
    use Concerns\PrepareBuilder;

    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto,
        private readonly HttpTransporter $httpTransporter,
        private readonly FormRedirectTransporter $formRedirectTransporter
    ) {
        //
    }

    public function payment(): MPGBuilder
    {
        return $this->prepareBuilder((new MPGBuilder(
            $this->factory, $this->crypto, $this->httpTransporter
        ))->setFormRedirectTransporter($this->formRedirectTransporter));
    }

    public function __call(string $method, array $parameters)
    {
        return $this->payment()->$method(...$parameters);
    }
}
