<?php

namespace W4ll4se\NewebPay;

use W4ll4se\NewebPay\Traits\EncryptionTrait;
use W4ll4se\NewebPay\Traits\RequestTrait;

class NewebPayClose
{
    use EncryptionTrait, RequestTrait;

    protected $NewebPayCloseURL;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    private $TradeData;

    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = $MerchantID;
        $this->HashKey = $HashKey;
        $this->HashIV = $HashIV;

        $this->setNewebPayCloseURL(config('newebpay.Debug'));

        $this->setRespondType();
        $this->setVersion();
        $this->TradeData['TimeStamp'] = time();
        $this->setNotifyURL();
    }

    private function setNewebPayCloseURL($debug = true)
    {
        if ($debug) {
            $this->NewebPayCloseURL = 'https://ccore.newebpay.com/API/CreditCard/Close';
        } else {
            $this->NewebPayCloseURL = 'https://core.newebpay.com/API/CreditCard/Close';
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
     * 設定請退款的模式
     *
     * no: MerchartOrderNo/TradeNo (商店訂單編號/藍新金流交易序號)
     * type: 
     *  'order': 使用商店訂單編號
     *  'trade': 使用藍新金流交易序號
     * 
     */
    public function setCloseOrder($no, $amt, $type = 'order')
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

    /*
     * 設定請款或退款
     *
     * type: 
     *  'pay': 請款
     *  'refund': 退款
     * 
     */
    public function setCloseType($type = 'pay')
    {
        if ($type === 'pay') {
            $this->TradeData['CloseType'] = 1;
        } else if ($type === 'refund') {
            $this->TradeData['CloseType'] = 2;
        }
        return $this;
    }

    public function setCancel($isCancel = false)
    {
        $this->TradeData['Cancel'] = $isCancel;
        return $this;
    }

    public function submit()
    {
        $postData = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);

        $request = [
            'MerchantID_' => $this->MerchantID,
            'PostData_' => $postData
        ];

        return $this->setRequestForm($request, $this->NewebPayCloseURL);
    }
}
