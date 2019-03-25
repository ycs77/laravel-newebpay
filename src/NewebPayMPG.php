<?php

namespace Treerful\NewebPay;

use Treerful\NewebPay\Traits\EncryptionTrait;
use Treerful\NewebPay\Traits\RequestTrait;
use DateTime;

class NewebPayMPG
{
    use EncryptionTrait, RequestTrait;

    private $NewebPayMPGURL;

    private $MerchantID;
    private $HashKey;
    private $HashIV;

    private $TradeData;

    private $Version;



    public function __construct($MerchantID = null, $HashKey = null, $HashIV = null)
    {
        $this->MerchantID = ($MerchantID != null ? $MerchantID : config('newebpay.MerchantID'));
        $this->HashKey = ($HashKey != null ? $HashKey : config('newebpay.HashKey'));
        $this->HashIV = ($HashIV != null ? $HashIV : config('newebpay.HashIV'));

        $this->setNewebPayMPGURL(config('newebpay.Debug'));

        $this->setRespondType(config('newebpay.RespondType'));
        $this->TradeData['TimeStamp'] = time();
        $this->setVersion(config('newebpay.Version'));
        $this->setLangType(config('newebpay.LangType'));
        $this->setTradeLimit(config('newebpay.TradeLimit'));
        $this->setExpireDate(config('newebpay.ExpireDate'));
        $this->setReturnURL(config('newebpay.ReturnURL'));
        $this->setNotifyURL(config('newebpay.NotifyURL'));
        $this->setCustomerURL(config('newebpay.CustomerURL'));
        $this->setClientBackURL(config('newebpay.ClientBackURLRL'));
        $this->setEmailModify(config('newebpay.EmailModify'));
        $this->setLoginType(config('newebpay.LoginType'));
        $this->setOrderComment(config('newebpay.OrderComment'));
        $this->setPaymentMethod(config('newebpay.PaymentMethod'));
        $this->setTokenTerm();
        $this->setCVSCOM(config('newebpay.CVSCOM'));

        $this->TradeData['MerchantID'] = $this->MerchantID;
    }

    private function setNewebPayMPGURL($debug = true)
    {
        if ($debug) {
            $this->NewebPayMPGURL = 'https://ccore.newebpay.com/MPG/mpg_gateway';
        } else {
            $this->NewebPayMPGURL = 'https://core.newebpay.com/MPG/mpg_gateway';
        }
    }

    public function setRespondType($type = 'JSON')
    {
        $this->TradeData['RespondType'] = $type;
        return $this;
    }

    public function setVersion($version = '1.5')
    {
        $this->Version = $version;
        $this->TradeData['Version'] = $version;
        return $this;
    }

    public function setLangType($lang = 'zh-tw')
    {
        $this->TradeData['LangType'] = $lang;
        return $this;
    }

    public function setTradeLimit($limit = 0)
    {
        $this->TradeData['TradeLimit'] = $limit != null ? $limit : 0;
        return $this;
    }

    public function setExpireDate($date = 7)
    {
        $now = new DateTime;
        $this->TradeData['ExpireDate'] = $now->modify('+ ' . $date . ' day')->format('Ymd');
        return $this;
    }

    public function setReturnURL($url = null)
    {
        $this->TradeData['ReturnURL'] = $url;
        return $this;
    }

    public function setNotifyURL($url = null)
    {
        $this->TradeData['NotifyURL'] = $url;
        return $this;
    }

    public function setCustomerURL($url = null)
    {
        $this->TradeData['CustomerURL'] = $url;
        return $this;
    }

    public function setClientBackURL($url = null)
    {
        $this->TradeData['ClientBackURL'] = $url;
        return $this;
    }

    public function setEmailModify($isModify = false)
    {
        $this->TradeData['EmailModify'] = $isModify ? 1 : 0;
        return $this;
    }

    public function setLoginType($isLogin = false)
    {
        $this->TradeData['LoginType'] = $isLogin ? 1 : 0;
        return $this;
    }

    public function setOrderComment($comment = "")
    {
        $this->TradeData['OrderComment'] = $comment;
        return $this;
    }

    public function setPaymentMethod($arrPaymentMethod)
    {
        $this->TradeData['CREDIT'] = $arrPaymentMethod['CREDIT']['Enable'] ? 1 : 0;
        $this->TradeData['ANDROIDPAY'] = $arrPaymentMethod['ANDROIDPAY'] ? 1 : 0;
        $this->TradeData['SAMSUNGPAY'] = $arrPaymentMethod['SAMSUNGPAY'] ? 1 : 0;
        $this->TradeData['InstFlag'] = ($arrPaymentMethod['CREDIT']['Enable'] and $arrPaymentMethod['CREDIT']['InstFlag']) ? $arrPaymentMethod['CREDIT']['InstFlag'] : 0;
        $this->TradeData['CreditRed'] = ($arrPaymentMethod['CREDIT']['Enable'] and $arrPaymentMethod['CREDIT']['CreditRed']) ? 1 : 0;
        $this->TradeData['UNIONPAY'] = $arrPaymentMethod['UNIONPAY'] ? 1 : 0;
        $this->TradeData['WEBATM'] = $arrPaymentMethod['WEBATM'] ? 1 : 0;
        $this->TradeData['VACC'] = $arrPaymentMethod['VACC'] ? 1 : 0;
        $this->TradeData['CVS'] = $arrPaymentMethod['CVS'] ? 1 : 0;
        $this->TradeData['BARCODE'] = $arrPaymentMethod['BARCODE'] ? 1 : 0;
        $this->TradeData['P2G'] = $arrPaymentMethod['P2G'] ? 1 : 0;
        return $this;
    }

    public function setCVSCOM($cvscom = 0)
    {
        $this->TradeData['CVSCOM'] = $cvscom != null ? $cvscom : 0;
        return $this;
    }

    public function setTokenTerm($token = "")
    {
        $this->TradeData['TokenTerm'] = $token;
        return $this;
    }

    public function setOrder($no, $amt, $desc, $email)
    {
        $this->TradeData['MerchantOrderNo'] = $no;
        $this->TradeData['Amt'] = $amt;
        $this->TradeData['ItemDesc'] = $desc;
        $this->TradeData['Email'] = $email;
        return $this;
    }

    // public function getProperties()
    // {
    //     return get_object_vars($this);
    // }

    public function submit()
    {
        $tradeInfo = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);
        $tradeSha = $this->encryptDataBySHA($tradeInfo, $this->HashKey, $this->HashIV);

        $request = [
            'MerchantID' => $this->MerchantID,
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
            'Version' => $this->Version
        ];


        return $this->setRequestForm($request, $this->NewebPayMPGURL);
    }
}
