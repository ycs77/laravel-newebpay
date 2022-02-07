<?php

namespace Ycs77\NewebPay\Concerns;

trait HasEncryption
{
    /**
     * Encrypt data with AES.
     *
     * @param  array  $parameter
     * @param  string  $hashKey
     * @param  string  $hashIV
     * @return string
     */
    protected function encryptDataByAES($parameter, $hashKey, $hashIV)
    {
        $postDataStr = http_build_query($parameter);

        return trim(bin2hex(openssl_encrypt($this->addPadding($postDataStr), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV)));
    }

    /**
     * Decrypt data with AES.
     *
     * @param  string  $parameter
     * @param  string  $hashKey
     * @param  string  $hashIV
     * @return string|false
     */
    protected function decryptDataByAES($parameter, $hashKey, $hashIV)
    {
        return $this->stripPadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV));
    }

    /**
     * Encrypt data with SHA.
     *
     * @param  string  $parameter
     * @param  string  $hashKey
     * @param  string  $hashIV
     * @return string
     */
    protected function encryptDataBySHA($parameter, $hashKey, $hashIV)
    {
        $postDataStr = 'HashKey='.$hashKey.'&'.$parameter.'&HashIV='.$hashIV;

        return strtoupper(hash('sha256', $postDataStr));
    }

    /**
     * Query check value.
     *
     * @param  array  $parameter
     * @param  string  $hashKey
     * @param  string  $hashIV
     * @return string
     */
    protected function queryCheckValue($parameter, $hashKey, $hashIV)
    {
        ksort($parameter);
        $checkStr = http_build_query($parameter);
        $postDataStr = 'IV='.$hashIV.'&'.$checkStr.'&Key='.$hashKey;

        return strtoupper(hash('sha256', $postDataStr));
    }

    /**
     * Add padding.
     *
     * @param  string  $string
     * @param  int  $blocksize
     * @return string
     */
    protected function addPadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);

        return $string;
    }

    /**
     * Strip padding.
     *
     * @param  string  $string
     * @return string|false
     */
    protected function stripPadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if (preg_match('/'.$slastc.'{'.$slast.'}/', $string)) {
            $string = substr($string, 0, strlen($string) - $slast);

            return $string;
        }

        return false;
    }
}
