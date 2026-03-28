<?php

namespace Ycs77\NewebPay\Url;

class UrlFormatter
{
    public function __construct(
        protected PrependAppUrl $prependAppUrlAction,
        protected WithSessionIdKey $withSessionIdKeyAction
    ) {
        //
    }

    public function formatCallbackUrl(string $url): string
    {
        return $this->prependAppUrlAction->handle($url);
    }

    public function withSessionIdKey(string $url): string
    {
        return $this->withSessionIdKeyAction->handle($url);
    }
}
