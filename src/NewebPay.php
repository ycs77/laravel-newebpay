<?php

namespace W4ll4se\NewebPay;

use W4ll4se\NewebPay\Traits\EncryptionTrait;

class NewebPay
{

    use EncryptionTrait;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = ($MerchantID != null ? $MerchantID : config('newebpay.MerchantID'));
        $this->HashKey = ($HashKey != null ? $HashKey : config('newebpay.HashKey'));
        $this->HashIV = ($HashIV != null ? $HashIV : config('newebpay.HashIV'));
    }


    /*
     * 付款
     * 
     * no: 訂單編號
     * amt: 訂單金額
     * desc: 商品描述
     * email: 連絡信箱
     */ 
    public function payment($no, $amt, $desc, $email)
    {
        $newebPay = new NewebPayMPG($this->MerchantID, $this->HashKey, $this->HashIV);
        $newebPay->setOrder($no, $amt, $desc, $email);
        return $newebPay;
    }

    /*
     * 取消授權
     * 
     * no: 訂單編號
     * amt: 訂單金額
     * type:
     *  'order' => 使用商店訂單編號追蹤
     *  'trade' => 使用藍新金流交易序號追蹤
     */ 
    public function creditCancel($no, $amt, $type = 'order')
    { 
        $newebPay = new NewebPayCancel($this->MerchantID, $this->HashKey, $this->HashIV);
        $newebPay->setCancelOrder($no, $amt, $type);

        return $newebPay;
    }

    /*
     * 請款
     *
     * no: 訂單編號
     * amt: 訂單金額
     * type: 
     *  'order' => 使用商店訂單編號追蹤
     *  'trade' => 使用藍新金流交易序號追蹤
     */ 
    public function requestPayment($no, $amt, $type = 'order')
    {
        $newebPay = new NewebPayClose($this->MerchantID, $this->HashKey, $this->HashIV);
        $newebPay->setCloseOrder($no, $amt, $type);
        $newebPay->setCloseType('pay');

        return $newebPay;
    }

     /*
      * 退款
      *
      * no: 訂單編號
      * amt: 訂單金額
      * type: 
      *  'order' => 使用商店訂單編號追蹤
      *  'trade' => 使用藍新金流交易序號追蹤
      */ 
    public function requestRefund($no, $amt, $type = 'order')
    {
        $newebPay = new NewebPayClose($this->MerchantID, $this->HashKey, $this->HashIV);
        $newebPay->setCloseOrder($no, $amt, $type);
        $newebPay->setCloseType('refund');

        return $newebPay;
    }


    public function decodeCallback($encryptString)
    {
        $decryptString = $this->decryptDataByAES($encryptString, $this->HashKey, $this->HashIV);

        return json_decode($decryptString, true);
    }
}

