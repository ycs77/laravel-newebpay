<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Resources\Customer;
use Ycs77\NewebPay\Resources\Payment;
use Ycs77\NewebPay\Resources\PaymentResult;
use Ycs77\NewebPay\Results\MPG\CustomerResult;
use Ycs77\NewebPay\Results\MPG\PaymentResult as MPGPaymentResult;

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

    /**
     * MPG (多功能收款) - 處理一次性支付交易
     * 支援信用卡、ATM、超商代碼等多種支付方式
     */
    public function payment(): Payment
    {
        return new Payment(
            $this, $this->crypto, $this->httpTransporter, $this->formRedirectTransporter
        );
    }

    /**
     * 解析並回傳交易結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function result(Request $request): MPGPaymentResult
    {
        return (new PaymentResult(
            $this, $this->crypto
        ))->result($request);
    }

    /**
     * 解析並回傳付款取號結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function customer(Request $request): CustomerResult
    {
        return (new Customer(
            $this, $this->crypto
        ))->customer($request);
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
