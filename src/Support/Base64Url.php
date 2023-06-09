<?php

namespace Ycs77\NewebPay\Support;

class Base64Url
{
    /**
     * Encoding base64 data for URL.
     */
    public static function encode(string $data): string
    {
        return rtrim(strtr($data, '+/', '-_'), '=');
    }

    /**
     * Decoding base64 data from URL.
     */
    public static function decode(string $data): string
    {
        return str_pad(strtr($data, '-_', '+/'), ceil(strlen($data) / 4) * 4, '=', STR_PAD_RIGHT);
    }
}
