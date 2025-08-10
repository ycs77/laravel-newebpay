<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Builders\Payment\PaymentBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;

class Factory
{
    /**
     * NewebPay 生產環境的 baseURL。
     */
    protected string $productionBaseUrl = 'https://core.newebpay.com';

    /**
     * NewebPay 測試環境的 baseURL。
     */
    protected string $testingBaseUrl = 'https://ccore.newebpay.com';

    public function __construct(
        protected Crypto $crypto,
        protected FormRedirectTransporter $formRedirectTransporter,
        protected HttpTransporter $httpTransporter,
        protected array $config
    ) {
        //
    }

    public function payment(): PaymentBuilder
    {
        return new PaymentBuilder(
            $this, $this->crypto, $this->formRedirectTransporter, $this->httpTransporter
        );
    }

    public function baseUrl(): string
    {
        return $this->config['env'] === 'production'
            ? $this->productionBaseUrl
            : $this->testingBaseUrl;
    }

    public function config(?string $key = null)
    {
        if (isset($key)) {
            return $this->config[$key] ?? null;
        }

        return $this->config;
    }
}
