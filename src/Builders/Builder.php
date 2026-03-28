<?php

namespace Ycs77\NewebPay\Builders;

use Illuminate\Http\Response;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Exceptions\NewebPayException;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;

abstract class Builder
{
    use Concerns\HasPrepareOptions;
    use Conditionable;
    use Tappable;

    /**
     * API 端點
     */
    protected string $endpoint = '';

    /**
     * 已經設定完成、且可準備送出的的選項
     */
    protected ?Options $preparedOptions = null;

    protected ?FormRedirectTransporter $formRedirectTransporter = null;

    public function __construct(
        protected Factory $factory,
        protected Crypto $crypto,
        protected HttpTransporter $httpTransporter,
        protected array $config
    ) {
        $this->boot();
    }

    abstract protected function boot(): void;

    abstract public function options(): Options;

    /**
     * 發送 API 請求到 NewebPay
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebPayException
     */
    protected function sendRequest(): array
    {
        $requestData = $this->toRequestData();

        $this->httpTransporter->setTimeout($this->config['timeout']);

        $response = $this->httpTransporter->send(
            $requestData['url'],
            $requestData['formData']
        );

        $data = $response->json();

        if ($data['Status'] !== 'SUCCESS') {
            throw new NewebPayException(
                $requestData['url'], $requestData['formData'], $data['Status'], $data['Message']
            );
        }

        return $data;
    }

    /**
     * 取得 HTTP 請求數據
     */
    public function toRequestData(): array
    {
        $url = $this->factory->baseUrl().$this->endpoint;
        $options = $this->getPreparedOptions();
        $formData = $options->toArray();

        // 如果有 TradeInfo 則進行加密和產生 SHA
        if (isset($formData['TradeInfo'])) {
            $formData['TradeInfo'] = $this->crypto->encryptByAES(
                $formData['TradeInfo']
            );

            $formData['TradeSha'] = $this->crypto->hashBySHA(
                $formData['TradeInfo']
            );
        }

        // 如果有 CheckValue 則進行產生檢查碼
        if (isset($formData['CheckValue'])) {
            $formData['CheckValue'] = $this->crypto->encodeCheckValue(
                $formData['CheckValue']
            );
        }

        // 如果有 PostData_ 則進行加密
        if (isset($formData['PostData_'])) {
            $formData['PostData_'] = $this->crypto->encryptByAES(
                $formData['PostData_']
            );
        }

        return [
            'url' => $url,
            'formData' => $formData,
        ];
    }

    /**
     * 解析並設定完成可準備送出的的選項
     */
    protected function getPreparedOptions(): Options
    {
        if ($this->preparedOptions) {
            return $this->preparedOptions;
        }

        $options = $this->options();

        if ($this->onPreparedOptionsCallback) {
            call_user_func($this->onPreparedOptionsCallback, $options);
        }

        $this->preparedOptions = $options;

        return $options;
    }

    /**
     * 發送跳轉到 NewebPay 的請求
     */
    protected function sendFormRedirectRequest(): Response
    {
        $requestData = $this->toRequestData();

        return $this->formRedirectTransporter->send(
            $requestData['url'],
            $requestData['formData']
        );
    }

    /**
     * 發送跳轉到 NewebPay 平台的請求表單資料
     */
    public function toRedirectRequestData(): array
    {
        return $this->toRequestData();
    }

    public function setFormRedirectTransporter(FormRedirectTransporter $formRedirectTransporter): static
    {
        $this->formRedirectTransporter = $formRedirectTransporter;

        return $this;
    }
}
