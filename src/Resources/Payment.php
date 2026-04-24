<?php

namespace Ycs77\NewebPay\Resources;

use Ycs77\NewebPay\Builders\Trade\MPGBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Url\WithSessionIdKey;

/**
 * @mixin \Ycs77\NewebPay\Builders\Trade\MPGBuilder
 */
final class Payment
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto,
        private readonly HttpTransporter $httpTransporter,
        private readonly FormRedirectTransporter $formRedirectTransporter,
        private readonly WithSessionIdKey $withSessionIdKey
    ) {
        //
    }

    public function payment(): MPGBuilder
    {
        return (new MPGBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->factory->config()
        ))->setFormRedirectTransporter($this->formRedirectTransporter);
    }

    public function __call(string $method, array $parameters)
    {
        return $this->payment()->$method(...$parameters);
    }
}
