<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Carbon;

abstract class BaseNewebPay
{
    use Concerns\HasEncryption;
    use Concerns\HasSender;
    use Concerns\TradeData;

    protected Config $config;

    /**
     * The newebpay MerchantID.
     */
    protected string $MerchantID;

    /**
     * The newebpay HashKey.
     */
    protected string $HashKey;

    /**
     * The newebpay HashIV.
     */
    protected string $HashIV;

    /**
     * The newebpay URL.
     */
    protected string $url;

    /**
     * The newebpay production base URL.
     */
    protected string $productionUrl = 'https://core.newebpay.com/';

    /**
     * The newebpay test base URL.
     */
    protected string $testUrl = 'https://ccore.newebpay.com/';

    /**
     * Now timestamp.
     */
    protected int $timestamp;

    /**
     * Create a new base newebpay instance.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->MerchantID = $this->config->get('newebpay.merchant_id');
        $this->HashKey = $this->config->get('newebpay.hash_key');
        $this->HashIV = $this->config->get('newebpay.hash_iv');

        $this->setTimestamp();
        $this->tradeDataBoot();
        $this->boot();
    }

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        //
    }

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
    public function setApiPath(string $path): self
    {
        $this->url = $this->generateUrl($path);

        return $this;
    }

    /**
     * Set now timestamp.
     */
    public function setTimestamp(): self
    {
        $this->timestamp = Carbon::now()->timestamp;

        return $this;
    }

    /**
     * Get request data.
     */
    public function getRequestData(): array
    {
        return [];
    }

    /**
     * Submit data to newebpay API.
     */
    public function submit(): mixed
    {
        return $this->sender->send($this->getRequestData(), $this->url);
    }
}
