<?php

namespace W4ll4se\NewebPay;

use W4ll4se\NewebPay\Traits\EncryptionTrait;
use W4ll4se\NewebPay\Traits\RequestTrait;

class NewebPayCancel
{
    use EncryptionTrait, RequestTrait;

    protected $NewebPayCancelURL;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    private $TradeData;

    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = $MerchantID;
        $this->HashKey = $HashKey;
        $this->HashIV = $HashIV;

        $this->setNewebPayCancelURL(config('newebpay.Debug'));

        $this->setRespondType();
        $this->setVersion();
        $this->TradeData['TimeStamp'] = time();
        $this->setNotifyURL();
    }

    private function setNewebPayCancelURL($debug = true)
    {
        if ($debug) {
            $this->NewebPayCancelURL = 'https://ccore.newebpay.com/API/CreditCard/Cancel';
        } else {
            $this->NewebPayCancelURL = 'https://core.newebpay.com/API/CreditCard/Cancel';
        }
    }

    public function setRespondType($type = 'JSON')
    {
        $this->TradeData['RespondType'] = $type;
        return $this;
    }

    public function setVersion($version = 1.0)
    {
        $this->TradeData['Version'] = $version;
        return $this;
    }

    /*
     * 設定取消授權的模式
     *
     * no: MerchartOrderNo/TradeNo (商店訂單編號/藍新金流交易序號)
     * type: 
     *  'order': 使用商店訂單編號
     *  'trade': 使用藍新金流交易序號
     * 
     */
    public function setCancelOrder($no, $amt, $type = 'order')
    {

        if ($type === 'order') {
            $this->TradeData['MerchantOrderNo'] = $no;
            $this->TradeData['IndexType'] = 1;
        } else if ($type === 'trade') {
            $this->TradeData['TradeNo'] = $no;
            $this->TradeData['IndexType'] = 2;
        }

        $this->TradeData['Amt'] = $amt;
        return $this;
    }

    public function setNotifyURL($url = null)
    {
        $this->TradeData['NotifyURL'] = $url;
        return $this;
    }

    public function submit()
    {
        $postData = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);

        $request = [
            'MerchantID_' => $this->MerchantID,
            'PostData_' => $postData
        ];

        return $this->setRequestForm($request, $this->NewebPayCancelURL);
    }
}
