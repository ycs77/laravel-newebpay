<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Ycs77\NewebPay\Contracts\UserSourceable;

abstract class BaseNewebPay
{
    use Concerns\HasEncryption;
    use Concerns\HasSender;

    /**
     * The newebpay MerchantID.
     */
    protected string $merchantID;

    /**
     * The newebpay HashKey.
     */
    protected string $hashKey;

    /**
     * The newebpay HashIV.
     */
    protected string $hashIV;

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
     * Now timestamp.
     */
    protected int $timestamp;

    /**
     * Create a new base newebpay instance.
     */
    public function __construct(
        protected Config $config,
        protected Session $session
    ) {
        $this->merchantID = $this->config->get('newebpay.merchant_id');
        $this->hashKey = $this->config->get('newebpay.hash_key');
        $this->hashIV = $this->config->get('newebpay.hash_iv');

        $this->setTimestamp();
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
    public function apiPath(string $path)
    {
        $this->url = $this->generateUrl($path);

        return $this;
    }

    /**
     * Set now timestamp.
     */
    public function setTimestamp()
    {
        $this->timestamp = Carbon::now()->timestamp;

        return $this;
    }

    /**
     * Get request data.
     */
    abstract public function requestData(): array;

    /**
     * Submit data to newebpay API.
     */
    public function submit(HttpRequest $request = null): mixed
    {
        if ($this->sender instanceof UserSourceable) {
            $this->sender->preserveUserSource($request ?? Request::instance());
        }

        return $this->sender->send($this->requestData(), $this->url);
    }
}
