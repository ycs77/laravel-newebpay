<?php

namespace Ycs77\NewebPay\Resources;

use Ycs77\NewebPay\Builders\Period\AlterBuilder;
use Ycs77\NewebPay\Builders\Period\AlterStatusBuilder;
use Ycs77\NewebPay\Builders\Period\CreateBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Url\WithSessionIdKey;

final class Period
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

    public function create(): CreateBuilder
    {
        return (new CreateBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->factory->config()
        ))->setFormRedirectTransporter($this->formRedirectTransporter);
    }

    public function alterStatus(): AlterStatusBuilder
    {
        return new AlterStatusBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->factory->config()
        );
    }

    public function alter(): AlterBuilder
    {
        return new AlterBuilder(
            $this->factory, $this->crypto, $this->httpTransporter, $this->factory->config()
        );
    }
}
