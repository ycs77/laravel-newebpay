<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Request;
use Ycs77\LaravelRecoverSession\Facades\RecoverSession;

/**
 * @property \Illuminate\Contracts\Config\Repository $config
 */
trait WithSessionIdKey
{
    protected string $urlSessionIdKey = 'sid';

    public function withSessionIdKey(string $urlType)
    {
        if ($this->config->get('newebpay.with_session_id') &&
            is_string($this->dataForWithSessionId()[$urlType])
        ) {
            $data = $this->dataForWithSessionId();
            $key = RecoverSession::preserve(Request::instance());
            $delimiter = str_contains($data[$urlType], '?') ? '&' : '?';

            $data[$urlType] = $data[$urlType].$delimiter.$this->urlSessionIdKey.'='.$key;

            $this->dataForWithSessionId($data);
        }

        return $this;
    }

    public function urlSessionIdKey(string $key)
    {
        $this->urlSessionIdKey = $key;

        return $this;
    }

    protected function dataForWithSessionId(array $data = null): array
    {
        if ($data) {
            $this->tradeData = $data;
        }

        return $this->tradeData;
    }
}
