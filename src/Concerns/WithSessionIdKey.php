<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Request;
use Ycs77\LaravelRecoverSession\Facades\RecoverSession;

/**
 * @property \Illuminate\Contracts\Config\Repository $config
 */
trait WithSessionIdKey
{
    public function withSessionIdKey(string $urlType)
    {
        if ($this->config->get('newebpay.with_session_id') &&
            is_string($this->dataForWithSessionId()[$urlType])
        ) {
            $urlSessionIdKey = $this->config->get('recover-session.session_id_key');
            $data = $this->dataForWithSessionId();
            $key = RecoverSession::preserve(Request::instance());
            $delimiter = str_contains($data[$urlType], '?') ? '&' : '?';

            $data[$urlType] = $data[$urlType].$delimiter.$urlSessionIdKey.'='.$key;

            $this->dataForWithSessionId($data);
        }

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
