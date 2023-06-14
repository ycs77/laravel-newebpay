<?php

namespace Ycs77\NewebPay\Concerns;

use Throwable;
use Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException;

trait HasEncryption
{
    protected function encryptDataByAES(array $parameter, string $hashKey, string $hashIV, string $type = 'String'): string
    {
        $postDataStr = $type === 'JSON'
            ? json_encode($parameter)
            : http_build_query($parameter);

        return trim(bin2hex(openssl_encrypt($this->addPadding($postDataStr), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV)));
    }

    protected function decryptDataByAES(string $parameter, string $hashKey, string $hashIV): string|false
    {
        return $this->stripPadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV));
    }

    protected function encryptDataBySHA(string $parameter, string $hashKey, string $hashIV): string
    {
        $hashs = 'HashKey='.$hashKey.'&'.$parameter.'&HashIV='.$hashIV;

        return strtoupper(hash('sha256', $hashs));
    }

    protected function queryCheckValue(array $parameter, string $hashKey, string $hashIV): string
    {
        ksort($parameter);
        $checkStr = http_build_query($parameter);
        $hashs = 'IV='.$hashIV.'&'.$checkStr.'&Key='.$hashKey;

        return strtoupper(hash('sha256', $hashs));
    }

    protected function addPadding(string $string, int $blocksize = 32): string
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);

        return $string;
    }

    protected function stripPadding(string $string): string|false
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);

        if (preg_match('/'.$slastc.'{'.$slast.'}/', $string)) {
            $string = substr($string, 0, strlen($string) - $slast);

            return $string;
        }

        return false;
    }

    /**
     * 解碼加密字串
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    protected function decode(string $encryptString): mixed
    {
        try {
            $decryptString = $this->decryptDataByAES($encryptString, $this->hashKey, $this->hashIV);

            return json_decode($decryptString, true);
        } catch (Throwable $e) {
            throw new NewebpayDecodeFailException($e, $encryptString);
        }
    }
}
