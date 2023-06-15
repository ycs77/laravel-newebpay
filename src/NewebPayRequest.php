<?php

namespace Ycs77\NewebPay;

abstract class NewebPayRequest extends NewebPay
{
    use Concerns\HasSender;

    /**
     * The newebpay URL.
     */
    protected string $url;

    /**
     * The newebpay production base URL.
     */
    protected string $productionUrl = 'https://core.newebpay.com';

    /**
     * The newebpay test base URL.
     */
    protected string $testUrl = 'https://ccore.newebpay.com';

    /**
     * Generate the newebpay full URL.
     */
    public function generateUrl(string $path): string
    {
        return ($this->config->get('newebpay.debug') ? $this->testUrl : $this->productionUrl).$path;
    }

    /**
     * Get the newebpay full URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the newebpay API path.
     */
    public function apiPath(string $path)
    {
        $this->url = $this->generateUrl($path);

        return $this;
    }

    /**
     * Get request data.
     */
    abstract public function requestData(): array;

    /**
     * Submit data to newebpay API.
     */
    public function submit(): mixed
    {
        return $this->sender->send($this->requestData(), $this->url);
    }
}
