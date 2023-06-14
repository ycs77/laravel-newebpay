<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Carbon;

abstract class NewebPay
{
    use Concerns\HasEncryption;

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
     * Set now timestamp.
     */
    public function setTimestamp()
    {
        $this->timestamp = Carbon::now()->timestamp;

        return $this;
    }
}
