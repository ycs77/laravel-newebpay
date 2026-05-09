<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;
use Ycs77\NewebPay\Callback\Period\CreateCallbackResult;
use Ycs77\NewebPay\Callback\Period\NotifyCallbackResult;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Resources\CreditCard;
use Ycs77\NewebPay\Resources\Customer;
use Ycs77\NewebPay\Resources\Payment;
use Ycs77\NewebPay\Resources\PaymentQuery;
use Ycs77\NewebPay\Resources\PaymentResult;
use Ycs77\NewebPay\Resources\Period;
use Ycs77\NewebPay\Results\Period\CreateResult;
use Ycs77\NewebPay\Results\Period\NotifyResult;
use Ycs77\NewebPay\Results\Result;
use Ycs77\NewebPay\Results\Trade\CustomerResult;
use Ycs77\NewebPay\Results\Trade\PaymentResult as MPGPaymentResult;
use Ycs77\NewebPay\Testing\TestRequest;
use Ycs77\NewebPay\Url\PrependAppUrl;
use Ycs77\NewebPay\Url\WithSessionIdKey;

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

    /**
     * 是否開啟紀錄假資料模式
     */
    protected bool $recording = false;

    /**
     * 已紀錄的請求選項
     *
     * @var TestRequest[]
     */
    protected array $requests = [];

    /**
     * 已設定的假回傳資料
     *
     * @var Result[]
     */
    protected array $results = [];

    public function __construct(
        protected Crypto $crypto,
        protected FormRedirectTransporter $formRedirectTransporter,
        protected HttpTransporter $httpTransporter,
        protected WithSessionIdKey $withSessionIdKey,
        protected PrependAppUrl $prependAppUrl,
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
            $this, $this->crypto, $this->httpTransporter, $this->formRedirectTransporter, $this->withSessionIdKey, $this->prependAppUrl
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
            $this, $this->crypto, $this->httpTransporter, $this->formRedirectTransporter, $this->withSessionIdKey, $this->prependAppUrl
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

    /**
     * 設定假回傳資料
     *
     * @param  Result[]  $results
     */
    public function fake(array $results): void
    {
        $this->recording = true;

        $this->results = $results;
    }

    /**
     * 目前是否開啟紀錄假資料模式
     */
    public function recording(): bool
    {
        return $this->recording;
    }

    /**
     * 紀錄請求選項，並回傳模擬資料
     *
     * @throws RuntimeException
     */
    public function record(string $resource, ?string $action, Options $options): ?Result
    {
        if (! $this->recording) {
            return null;
        }

        $this->requests[] = new TestRequest(
            $resource, $action, $options
        );

        /** @var Result|null $result */
        $result = array_shift($this->results);

        if (is_null($result)) {
            throw new RuntimeException('No more fake results available.');
        }

        return $result;
    }

    /**
     * 斷言已經送出指定的請求
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function assertSent(string $resource, string|callable|null $action, ?callable $callback = null): void
    {
        $resourceName = "[{$resource}".(is_string($action) ? "::{$action}" : '').']';

        PHPUnit::assertTrue(
            $this->sent($resource, $action, $callback) !== [],
            "The expected {$resourceName} request was not sent."
        );
    }

    /**
     * 斷言沒有送出指定的請求
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function assertNotSent(string $resource, string|callable|null $action, ?callable $callback = null): void
    {
        $resourceName = "[{$resource}".(is_string($action) ? "::{$action}" : '').']';

        PHPUnit::assertTrue(
            $this->sent($resource, $action, $callback) === [],
            "The unexpected {$resourceName} request was sent."
        );
    }

    protected function sent(string $resource, string|callable|null $action, ?callable $callback): array
    {
        if (is_callable($action) && is_null($callback)) {
            $callback = $action;
            $action = null;
        }

        $requestOptions = $this->resourcesOf($resource, $action);

        if ($requestOptions === []) {
            return [];
        }

        $callback = $callback ?: fn (): bool => true;

        return array_filter($requestOptions, fn (TestRequest $request) => $callback($request->options()));
    }

    /**
     * @return TestRequest[]
     */
    protected function resourcesOf(string $resource, ?string $action): array
    {
        return array_filter($this->requests, function (TestRequest $request) use ($resource, $action): bool {
            return $request->resource() === $resource
                && (is_null($action) || $request->action() === $action);
        });
    }
}
