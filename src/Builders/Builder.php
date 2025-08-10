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
    use Concerns\HasTransformOptions;
    use Conditionable;
    use Tappable;

    protected string $endpoint = '';

    public function __construct(
        protected Factory $factory,
        protected Crypto $crypto,
        protected FormRedirectTransporter $formRedirectTransporter,
        protected HttpTransporter $httpTransporter
    ) {
        $this->boot();
    }

    abstract protected function boot(): void;

    abstract public function getOptions(): Options;

    /**
     * 取得 HTTP 請求數據
     */
    public function toRequestData(): array
    {
        $url = $this->factory->baseUrl().$this->endpoint;
        $options = $this->getOptions();

        if ($this->transformOptionsCallback) {
            $options = call_user_func($this->transformOptionsCallback, $options);
        }

        $formData = $options->toArray();

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
     * 發送 Form Post 請求到 NewebPay
     */
    protected function sendFormPostRequest(): Response
    {
        $requestData = $this->toRequestData();

        return $this->formRedirectTransporter->send(
            $requestData['url'],
            $requestData['formData']
        );
    }

    /**
     * 發送 API 請求到 NewebPay
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebPayException
     */
    protected function sendRequest(): array
    {
        $requestData = $this->toRequestData();

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
}
