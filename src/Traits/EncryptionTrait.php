<?php

namespace Treerful\NewebPay\Traits;

trait EncryptionTrait
{

    private function encryptDataByAES($parameter, $hashKey, $hashIV)
    {
        $postDataStr = http_build_query($parameter);

        return trim(bin2hex(openssl_encrypt($this->addPadding($postDataStr), 'aes-256-cbc', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV)));
    }

    private function decryptDataByAES($parameter, $hashKey, $hashIV)
    {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV));
    }

    private function encryptDataBySHA($parameter, $hashKey, $hashIV)
    {
        $postDataStr = 'HashKey=' . $hashKey . '&' . $parameter . '&HashIV=' . $hashIV;

        return strtoupper(hash("sha256", $postDataStr));
    }

    private function addPadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);

        return $string;
    }

    private function strippadding($string)
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
