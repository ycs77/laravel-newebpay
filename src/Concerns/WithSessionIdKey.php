<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Facades\Request;
use Ycs77\LaravelRecoverSession\Facades\RecoverSession;

/**
 * @property \Illuminate\Contracts\Config\Repository $config
 */
trait WithSessionIdKey
{
    public function withSessionIdKey(?string $url)
    {
        if ($this->config->get('newebpay.with_session_id') && $url) {
            $urlSessionIdKey = $this->config->get('recover-session.session_id_key');

            $key = RecoverSession::preserve(Request::instance());

            $delimiter = str_contains($url, '?') ? '&' : '?';

            return $url.$delimiter.$urlSessionIdKey.'='.$key;
        }

        return $url;
    }
}
