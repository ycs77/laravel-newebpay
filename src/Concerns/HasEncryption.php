<?php

namespace Ycs77\NewebPay\Concerns;

trait HasEncryption
{
    protected function encryptDataByAES($parameter, $hashKey, $hashIV)
    {
        $postDataStr = http_build_query($parameter);

        return trim(bin2hex(openssl_encrypt($this->addPadding($postDataStr), 'aes-256-cbc', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV)));
    }

    protected function decryptDataByAES($parameter, $hashKey, $hashIV)
    {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV));
    }

    protected function encryptDataBySHA($parameter, $hashKey, $hashIV)
    {
        $postDataStr = 'HashKey=' . $hashKey . '&' . $parameter . '&HashIV=' . $hashIV;

        return strtoupper(hash("sha256", $postDataStr));
    }

    protected function queryCheckValue($parameter, $hashKey, $hashIV)
    {
        ksort($parameter);
        $checkStr = http_build_query($parameter);
        $postDataStr = 'IV=' . $hashIV . '&' . $checkStr . '&Key=' . $hashKey;

        return strtoupper(hash("sha256", $postDataStr));
    }

    protected function addPadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);

        return $string;
    }

    protected function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }
}
