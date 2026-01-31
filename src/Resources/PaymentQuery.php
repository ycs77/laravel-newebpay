<?php

namespace Ycs77\NewebPay\Resources;

use Ycs77\NewebPay\Builders\Trade\QueryBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;

/**
 * @mixin \Ycs77\NewebPay\Builders\Trade\QueryBuilder
 */
final class PaymentQuery
{
    public function __construct(
        private readonly Factory $factory,
        private readonly Crypto $crypto,
        private readonly HttpTransporter $httpTransporter
    ) {
        //
    }

    public function query(): QueryBuilder
    {
        return new QueryBuilder(
            $this->factory, $this->crypto, $this->httpTransporter
        );
    }

    public function __call(string $method, array $parameters)
    {
        return $this->query()->$method(...$parameters);
    }
}
