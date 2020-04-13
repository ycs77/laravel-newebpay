<?php

namespace Treerful\NewebPay;

use Treerful\NewebPay\Traits\EncryptionTrait;

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

    /*
      * 查詢
      *
      * no: 訂單編號
      * amt: 訂單金額
      */
    public function query($no, $amt)
    {
        $newebPay = new NewebPayQuery($this->MerchantID, $this->HashKey, $this->HashIV);
        $newebPay->setQuery($no, $amt);

        return $newebPay;
    }

    /** 
     * 信用卡授權 - 首次交易
     *
     * $data['no'] => 訂單編號
     * $data['email'] => 購買者 email
     * $data['cardNo'] => 信用卡號
     * $data['exp'] => 到期日 格式: 2021/01 -> 2101
     * $data['cvc'] => 信用卡驗證碼 格式: 3碼
     * $data['desc] => 商品描述
     * $data['amt'] => 綁定支付金額
     * $data['tokenTerm'] => 約定信用卡付款之付款人綁定資料
     */
    public function creditcardFirstTrade($data)
    {
        $newebPay = new NewebPayCreditCard($this->MerchantID . $this->HashKey, $this->HashIV);
        $newebPay->firstTrade($data);

        return $newebPay;
    }

    /** 
     * 信用卡授權 - 使用已綁定信用卡進行交易
     *
     * $data['no'] => 訂單編號
     * $data['amt'] => 訂單金額
     * $data['desc'] => 商品描述
     * $data['email'] => 購買者 email
     * $data['tokenValue'] => 綁定後取回的 token 值
     * $data['tokenTerm'] => 約定信用卡付款之付款人綁定資料 要與第一次綁定時一樣
     */
    public function creditcardTradeWithToken($data)
    {
        $newebPay = new NewebPayCreditCard($this->MerchantID . $this->HashKey, $this->HashIV);
        $newebPay->tradeWithToken($data);

        return $newebPay;
    }



    public function decodeCallback($encryptString)
    {
        $decryptString = $this->decryptDataByAES($encryptString, $this->HashKey, $this->HashIV);

        return json_decode($decryptString, true);
    }
}
