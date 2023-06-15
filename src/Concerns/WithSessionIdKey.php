<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Request;
use Ycs77\LaravelRecoverSession\Facades\RecoverSession;

/**
 * @property \Illuminate\Contracts\Config\Repository $config
 * @property array $tradeData
 */
trait WithSessionIdKey
{
    protected string $urlSessionIdKey = 'sid';

    public function withSessionIdKey(string $urlType)
    {
        if ($this->config->get('newebpay.with_session_id') &&
            is_string($this->tradeData[$urlType])
        ) {
            $key = RecoverSession::preserve(Request::instance());

            $delimiter = str_contains($this->tradeData[$urlType], '?') ? '&' : '?';

            $this->tradeData[$urlType] = $this->tradeData[$urlType].$delimiter.$this->urlSessionIdKey.'='.$key;
        }

        return $this;
    }

    public function urlSessionIdKey(string $key)
    {
        $this->urlSessionIdKey = $key;

        return $this;
    }
}
