<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Builders\Payment\PaymentBuilder;
use Ycs77\NewebPay\Callback\PaymentCallbackResult;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Results\Payment\PaymentResult;

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
        return (new PaymentBuilder(
            $this, $this->crypto, $this->httpTransporter
        ))->setFormRedirectTransporter($this->formRedirectTransporter);
    }

    /**
     * 解析並回傳交易結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function result(Request $request): PaymentResult
    {
        return (new PaymentCallbackResult(
            $this->crypto
        ))->result($request);
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
