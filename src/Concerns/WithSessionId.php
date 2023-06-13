<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Crypt;
use Ycs77\LaravelRecoverSession\Support\Base64Url;

/**
 * @property array $tradeData
 * @property \Illuminate\Contracts\Session\Session $session
 */
trait WithSessionId
{
    protected bool $withSessionId = true;
    protected string $urlSessionIdKey = 'sid';

    public function withSessionId(string $urlType, string $sessionId = null)
    {
        if (is_string($this->tradeData[$urlType])) {
            $sessionId = Crypt::encryptString($sessionId ?? $this->session->getId());

            $delimiter = str_contains($this->tradeData[$urlType], '?') ? '&' : '?';

            $this->tradeData[$urlType] = $this->tradeData[$urlType].$delimiter.$this->urlSessionIdKey.'='.Base64Url::encode($sessionId);
        }

        return $this;
    }

    public function setUrlSessionIdKey(string $key)
    {
        $this->urlSessionIdKey = $key;

        return $this;
    }
}
