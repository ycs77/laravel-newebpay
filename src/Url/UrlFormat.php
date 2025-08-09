<?php

namespace Ycs77\NewebPay\Url;

class UrlFormat
{
    public static function formatCallbackUrl(string $url)
    {
        /** @var \Ycs77\NewebPay\Url\PrependAppUrl */
        $prependAppUrl = app(PrependAppUrl::class);

        return $prependAppUrl->handle($url);
    }

    public static function withSessionIdKey(string $url)
    {
        /** @var \Ycs77\NewebPay\Url\WithSessionIdKey */
        $withSessionIdKey = app(WithSessionIdKey::class);

        return $withSessionIdKey->handle($url);
    }
}
