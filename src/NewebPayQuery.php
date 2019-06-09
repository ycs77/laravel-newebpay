<?php

namespace Treerful\NewebPay;

use Treerful\NewebPay\Traits\EncryptionTrait;
use Treerful\NewebPay\Traits\RequestTrait;

class NewebPayQuery
{
    use EncryptionTrait, RequestTrait;

    protected $NewebPayQueryURL;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    private $Version;
    private $RespondType;
    private $TimeStamp;
    private $CheckValue;

    private $CheckValueArr;

    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = $MerchantID;
        $this->HashKey = $HashKey;
        $this->HashIV = $HashIV;

        $this->CheckValueArr['MerchantID'] = $MerchantID;
        
        $this->setNewebPayQueryURL(config('newebpay.Debug'));

        $this->setVersion();
        $this->setRespondType();
        $this->TimeStamp = time();
    }

    private function setNewebPayQueryURL($debug = true)
    {
        if ($debug) {
            $this->NewebPayQueryURL = 'https://ccore.newebpay.com/API/QueryTradeInfo';
        } else {
            $this->NewebPayQueryURL = 'https://core.newebpay.com/API/QueryTradeInfo';
        }
    }

    public function setVersion($version = 1.1)
    {
        $this->Version = $version;
        return $this;
    }

    public function setRespondType($type = 'JSON')
    {
        $this->RespondType = $type;
        return $this;
    }

    public function setQuery($no, $amt)
    {
        $this->CheckValueArr['MerchantOrderNo'] = $no;
        $this->CheckValueArr['Amt'] = $amt;
        return $this;
    }

    public function submit()
    {
        $this->CheckValue = $this->queryCheckValue($this->CheckValueArr, $this->HashKey, $this->HashIV);

        $request = [
            'MerchantID' => $this->MerchantID,
            'Version' => $this->Version,
            'RespondType' => $this->RespondType,
            'CheckValue' => $this->CheckValue,
            'TimeStamp' => $this->TimeStamp,
            'MerchantOrderNo' => $this->CheckValueArr['MerchantOrderNo'],
            'Amt' => $this->CheckValueArr['Amt']
        ];

        return $this->setPostRequest($request, $this->NewebPayQueryURL);
    }

   
}
