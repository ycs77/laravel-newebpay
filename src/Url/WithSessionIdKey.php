<?php

namespace Ycs77\NewebPay\Url;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\Request;
use Ycs77\LaravelRecoverSession\RecoverSession;

class WithSessionIdKey
{
    public function __construct(
        protected Config $config,
        protected RecoverSession $recoverSession
    ) {
        //
    }

    public function handle(string $url): string
    {
        if ($this->config->get('newebpay.with_session_id') && $url) {
            $urlSessionIdKey = $this->config->get('recover-session.session_id_key');

            $key = $this->recoverSession->preserve(Request::instance());

            $delimiter = str_contains($url, '?') ? '&' : '?';

            return $url.$delimiter.$urlSessionIdKey.'='.$key;
        }

        return $url;
    }
}
