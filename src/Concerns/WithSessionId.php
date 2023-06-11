<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Crypt;
use Ycs77\LaravelRestoreSessionId\Support\Base64Url;

/**
 * @property array $TradeData
 * @property \Illuminate\Contracts\Session\Session $session
 */
trait WithSessionId
{
    protected string $urlSessionIdKey = 'sid';

    public function withSessionId(string $urlType, string $sessionId = null)
    {
        if (is_string($this->TradeData[$urlType])) {
            $sessionId = Crypt::encryptString($sessionId ?? $this->session->getId());

            $delimiter = str_contains($this->TradeData[$urlType], '?') ? '&' : '?';

            $this->TradeData[$urlType] = $this->TradeData[$urlType].$delimiter.$this->urlSessionIdKey.'='.Base64Url::encode($sessionId);
        }

        return $this;
    }

    public function setUrlSessionIdKey(string $key)
    {
        $this->urlSessionIdKey = $key;

        return $this;
    }
}
