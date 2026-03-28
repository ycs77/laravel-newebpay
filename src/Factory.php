<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Callback\Period\CreateCallbackResult;
use Ycs77\NewebPay\Callback\Period\NotifyCallbackResult;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Resources\CreditCard;
use Ycs77\NewebPay\Resources\Customer;
use Ycs77\NewebPay\Resources\Payment;
use Ycs77\NewebPay\Resources\PaymentQuery;
use Ycs77\NewebPay\Resources\PaymentResult;
use Ycs77\NewebPay\Resources\Period;
use Ycs77\NewebPay\Results\Period\CreateResult;
use Ycs77\NewebPay\Results\Period\NotifyResult;
use Ycs77\NewebPay\Results\Trade\CustomerResult;
use Ycs77\NewebPay\Results\Trade\PaymentResult as MPGPaymentResult;
use Ycs77\NewebPay\Url\UrlFormatter;

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
        protected UrlFormatter $urlFormatter,
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
            $this, $this->crypto, $this->httpTransporter, $this->formRedirectTransporter, $this->urlFormatter
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

    /**
     * 單筆交易查詢
     */
    public function query(): PaymentQuery
    {
        return new PaymentQuery(
            $this, $this->crypto, $this->httpTransporter
        );
    }

    /**
     * 信用卡相關功能
     */
    public function creditCard(): CreditCard
    {
        return new CreditCard(
            $this, $this->crypto, $this->httpTransporter
        );
    }

    /**
     * 信用卡定期定額委託相關功能
     */
    public function period(): Period
    {
        return new Period(
            $this, $this->crypto, $this->httpTransporter, $this->formRedirectTransporter, $this->urlFormatter
        );
    }

    /**
     * 解析並回傳定期定額委託結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function periodResult(Request $request): CreateResult
    {
        return (new CreateCallbackResult(
            $this->crypto, $this->config
        ))->result($request);
    }

    /**
     * 解析並回傳每期授權通知結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function periodNotify(Request $request): NotifyResult
    {
        return (new NotifyCallbackResult(
            $this->crypto, $this->config
        ))->result($request);
    }

    public function baseUrl(): string
    {
        return $this->config['env'] === 'production'
            ? $this->productionBaseUrl
            : $this->testingBaseUrl;
    }

    public function config(): array
    {
        return $this->config;
    }
}
