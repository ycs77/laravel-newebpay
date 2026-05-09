<?php

namespace Ycs77\NewebPay\Crypto;

use Ycs77\NewebPay\Contracts\CheckCodeVerifiable;
use Ycs77\NewebPay\Exceptions\DecryptException;
use Ycs77\NewebPay\Exceptions\EncryptException;
use Ycs77\NewebPay\Exceptions\InvalidCheckCodeException;
use Ycs77\NewebPay\Results\Result;

class Crypto
{
    protected string $hashKey;

    protected string $hashIV;

    /**
     * 使用 AES 加密
     *
     * @throws EncryptException
     */
    public function encryptByAES(array $data): string
    {
        $dataStr = http_build_query($data);

        $value = openssl_encrypt(
            $this->addPadding($dataStr),
            'AES-256-CBC',
            $this->hashKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->hashIV
        );

        if ($value === false) {
            throw new EncryptException('加密錯誤');
        }

        return trim(bin2hex($value));
    }

    /**
     * 使用 AES 解密
     *
     * @throws DecryptException
     */
    public function decryptByAES(string $encryptedData): array
    {
        $value = openssl_decrypt(
            hex2bin($encryptedData),
            'AES-256-CBC',
            $this->hashKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->hashIV
        );

        if ($value === false) {
            throw new DecryptException('解密錯誤');
        }

        $resultStr = $this->removePadding($value);

        $result = [];

        parse_str($resultStr, $result);

        return $result;
    }

    /**
     * 使用 SHA256 加密
     */
    public function hashBySHA(string $value): string
    {
        $value = 'HashKey='.$this->hashKey.'&'.$value.'&HashIV='.$this->hashIV;

        return strtoupper(hash('sha256', $value));
    }

    protected function addPadding(string $string, int $blocksize = 32): string
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);

        return $string.str_repeat(chr($pad), $pad);
    }

    protected function removePadding(string $string): string
    {
        $pad = ord(substr($string, -1));

        if ($pad < 1 || $pad > 32) {
            return $string; // No padding
        }

        return substr($string, 0, -$pad);
    }

    /**
     * 生成檢查碼
     */
    public function encodeCheckValue(array $data): string
    {
        ksort($data);
        $checkStr = http_build_query($data);

        return strtoupper(hash(
            'sha256', 'IV='.$this->hashIV.'&'.$checkStr.'&Key='.$this->hashKey
        ));
    }

    /**
     * 驗證檢查碼
     *
     * @throws InvalidCheckCodeException
     */
    public function verifyCheckCode(Result $result): void
    {
        if ($result instanceof CheckCodeVerifiable) {
            $checkCodeData = [
                'MerchantID' => $result->merchantID(),
                'Amt' => $result->amount(),
                'MerchantOrderNo' => $result->orderNo(),
                'TradeNo' => $result->tradeNo(),
            ];
            ksort($checkCodeData);
            $checkStr = http_build_query($checkCodeData);
            $checkCode = strtoupper(hash(
                'sha256', 'HashIV='.$this->hashIV.'&'.$checkStr.'&HashKey='.$this->hashKey
            ));

            if ($checkCode !== $result->checkCode()) {
                throw new InvalidCheckCodeException($checkCodeData + [
                    'CheckCode' => $result->checkCode(),
                ]);
            }
        }
    }

    public function setHashKey(string $hashKey): self
    {
        $this->hashKey = $hashKey;

        return $this;
    }

    public function setHashIv(string $hashIV): self
    {
        $this->hashIV = $hashIV;

        return $this;
    }
}
