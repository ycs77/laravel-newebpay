<?php

namespace Treerful\NewebPay;

use Treerful\NewebPay\Traits\EncryptionTrait;
use Treerful\NewebPay\Traits\RequestTrait;

class NewebPayCreditCard
{
    use EncryptionTrait, RequestTrait;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    private $NewebPayCreditCardURL;

    private $TradeData;

    private $responseType;

    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = $MerchantID;
        $this->HashKey = $HashKey;
        $this->HashIV = $HashIV;

        $this->setNewebPayQueryURL(config('newebpay.Debug'));

        $this->TradeData['TimeStamp'] = time();

        $this->setP3D(false);
        $this->setVersion();
        $this->setRespondType();
    }

    public function setNewebPayCreditCardURL($debug = true)
    {
        if ($debug) {
            // 信用卡授權測試網址
            $this->NewebPayCreditCardURL = "https://ccore.newebpay.com/API/CreditCard";
        } else {
            // 信用卡授權正式網址
            $this->NewebPayCreditCardURL = "https://core.newebpay.com/API/CreditCard";
        }
    }

    public function setVersion($version = "1.5")
    {
        $this->TradeData['Version'] = $version;
        return $this;
    }

    public function setResponseType($type = "JSON")
    {
        $this->responseType = $type;
        return $this;
    }

    // 3d 驗證交易
    public function setP3D($p3d = false)
    {
        // 需考慮傳送notify & return url when p3d is true;
        $this->TradeData['P3D'] = $p3d;
        return $this;
    }

    // 首次授權信用卡交易
    public function firstTrade($data)
    {
        $this->TradeData['TokenSwitch'] = 'get';

        $this->TradeData['MerchantOrderNo'] = $data['no'];
        $this->TradeData['Amt'] = $data['amt'];
        $this->TradeData['ProdDesc'] = $data['desc'];
        $this->TradeData['PayerEmail'] = $data['email'];
        $this->TradeData['CardNo'] = $data['cardNo'];
        $this->TradeData['Exp'] = $data['exp'];
        $this->TradeData['CVC'] = $data['cvc'];
        $this->TradeData['TokenTerm'] = $data['tokenTerm'];

        return $this;
    }

    public function tradeWithToken($data)
    {
        $this->TradeData['TokenSwitch'] = 'no';

        $this->TradeData['MerchantOrderNo'] = $data['no'];
        $this->TradeData['Amt'] = $data['amt'];
        $this->TradeData['ProdDesc'] = $data['desc'];
        $this->TradeData['PayerEmail'] = $data['email'];
        $this->TradeData['TokenValue'] = $data['tokenValue'];
        $this->TradeData['TokenTerm'] = $data['tokenTerm'];

        return $this;
    }

    public function submit()
    {
        $tradeInfo = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);

        $request = [
            'MerchantID_' => $this->MerchantID,
            'PostData_' => $tradeInfo,
            'Pos_' => $this->responseType
        ];

        return $this->setRequestForm($request, $this->NewebPayCreditCardURL);
    }
}
