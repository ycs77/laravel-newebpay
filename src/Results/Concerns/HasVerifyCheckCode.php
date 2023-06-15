<?php

namespace Ycs77\NewebPay\Results\Concerns;

trait HasVerifyCheckCode
{
    protected function verifyCheckCode(string $checkCode, array $parameter, string $hashKey, string $hashIV): bool
    {
        $parameter = [
            'MerchantID' => $parameter['MerchantID'],
            'Amt' => $parameter['Amt'],
            'MerchantOrderNo' => $parameter['MerchantOrderNo'],
            'TradeNo' => $parameter['TradeNo'],
        ];

        ksort($parameter);
        $checkStr = http_build_query($parameter);
        $hashs = 'HashIV='.$hashIV.'&'.$checkStr.'&HashKey='.$hashKey.'';
        $hashCode = strtoupper(hash('sha256', $hashs));

        return $checkCode === $hashCode;
    }
}
