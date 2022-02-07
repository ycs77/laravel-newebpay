<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Carbon;

trait TradeData
{
    /**
     * The newebpay trade data.
     *
     * @var array
     */
    protected $TradeData = [];

    /**
     * Bootstrap the trade data.
     *
     * @return void
     */
    public function tradeDataBoot()
    {
        $this->TradeData['TimeStamp'] = $this->timestamp;
        $this->setVersion();
        $this->setRespondType();
    }

    /**
     * Get the newebpay TradeData.
     *
     * @return array
     */
    public function getTradeData()
    {
        return $this->TradeData;
    }

    /**
     * 串接版本
     *
     * @param  string|null  $version
     * @return $this
     */
    public function setVersion($version = null)
    {
        $this->TradeData['Version'] = $version ?? $this->config->get('newebpay.Version');

        return $this;
    }

    /**
     * 回傳格式
     *
     * Support types: "JSON", "String"
     *
     * @param  string|null  $type
     * @return $this
     */
    public function setRespondType($type = null)
    {
        $this->TradeData['RespondType'] = $type ?? $this->config->get('newebpay.RespondType');

        return $this;
    }

    /**
     * 語系
     *
     * Support types: "zh-tw", "en"
     *
     * @param  string|null  $lang
     * @return $this
     */
    public function setLangType($lang = null)
    {
        $this->TradeData['LangType'] = $lang ?? $this->config->get('newebpay.LangType');

        return $this;
    }

    /**
     * 交易秒數限制
     *
     * 0: 不限制
     * 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算。
     * 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算。
     *
     * @param  int|null  $limit
     * @return $this
     */
    public function setTradeLimit($limit = null)
    {
        $this->TradeData['TradeLimit'] = $limit !== null ? $limit : $this->config->get('newebpay.TradeLimit');

        return $this;
    }

    /**
     * 繳費有效期限
     *
     * @param  int|null  $day
     * @return $this
     */
    public function setExpireDate($day = null)
    {
        $day = $day !== null ? $day : $this->config->get('newebpay.ExpireDate');

        $this->TradeData['ExpireDate'] = Carbon::now()->addDays($day)->format('Ymd');

        return $this;
    }

    /**
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 or 443
     *
     * @param  string|null  $url
     * @return $this
     */
    public function setReturnURL($url = null)
    {
        $this->TradeData['ReturnURL'] = $url ?? $this->config->get('newebpay.ReturnURL');

        return $this;
    }

    /**
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     * 僅接受 port 80 or 443
     *
     * @param  string|null  $url
     * @return $this
     */
    public function setNotifyURL($url = null)
    {
        $this->TradeData['NotifyURL'] = $url ?? $this->config->get('newebpay.NotifyURL');

        return $this;
    }

    /**
     * 商店取號網址
     *
     * 此參數若為空值，則會顯示取號結果在藍新金流頁面。
     *
     * @param  string|null  $url
     * @return $this
     */
    public function setCustomerURL($url = null)
    {
        $this->TradeData['CustomerURL'] = $url ?? $this->config->get('newebpay.CustomerURL');

        return $this;
    }

    /**
     * 付款取消-返回商店網址
     *
     * 當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面
     *
     * @param  string|null  $url
     * @return $this
     */
    public function setClientBackURL($url = null)
    {
        $this->TradeData['ClientBackURL'] = $url ?? $this->config->get('newebpay.ClientBackURL');

        return $this;
    }

    /**
     * 付款人電子信箱是否開放修改
     *
     * @param  bool|null  $isModify
     * @return $this
     */
    public function setEmailModify($isModify = null)
    {
        $this->TradeData['EmailModify'] = ($isModify !== null ? $isModify : $this->config->get('newebpay.EmailModify')) ? 1 : 0;

        return $this;
    }

    /**
     * 是否需要登入藍新金流會員
     *
     * @param  bool|null  $isLogin
     * @return $this
     */
    public function setLoginType($isLogin = false)
    {
        $this->TradeData['LoginType'] = ($isLogin !== null ? $isLogin : $this->config->get('newebpay.LoginType')) ? 1 : 0;

        return $this;
    }

    /**
     * 商店備註
     *
     * 1.限制長度為 300 字。
     * 2.若有提供此參數，將會於 MPG 頁面呈現商店備註內容。
     *
     * @param  string|null  $comment
     * @return $this
     */
    public function setOrderComment($comment = null)
    {
        $this->TradeData['OrderComment'] = $comment !== null ? $comment : $this->config->get('newebpay.OrderComment');

        return $this;
    }

    /**
     * 支付方式
     *
     * @param  array  $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod = [])
    {
        $paymentMethod = array_merge($this->config->get('newebpay.PaymentMethod'), $paymentMethod);

        $this->TradeData['CREDIT'] = $paymentMethod['CREDIT']['Enable'] ? 1 : 0;
        $this->TradeData['ANDROIDPAY'] = $paymentMethod['ANDROIDPAY'] ? 1 : 0;
        $this->TradeData['SAMSUNGPAY'] = $paymentMethod['SAMSUNGPAY'] ? 1 : 0;
        $this->TradeData['LINEPAY'] = $paymentMethod['LINEPAY'] ? 1 : 0;
        $this->TradeData['ImageUrl'] = isset($paymentMethod['ImageUrl']) && $paymentMethod['ImageUrl'] ? 1 : 0;
        $this->TradeData['InstFlag'] = ($paymentMethod['CREDIT']['Enable'] && $paymentMethod['CREDIT']['InstFlag']) ? $paymentMethod['CREDIT']['InstFlag'] : 0;
        $this->TradeData['CreditRed'] = ($paymentMethod['CREDIT']['Enable'] && $paymentMethod['CREDIT']['CreditRed']) ? 1 : 0;
        $this->TradeData['UNIONPAY'] = $paymentMethod['UNIONPAY'] ? 1 : 0;
        $this->TradeData['WEBATM'] = $paymentMethod['WEBATM'] ? 1 : 0;
        $this->TradeData['VACC'] = $paymentMethod['VACC'] ? 1 : 0;
        $this->TradeData['CVS'] = $paymentMethod['CVS'] ? 1 : 0;
        $this->TradeData['BARCODE'] = $paymentMethod['BARCODE'] ? 1 : 0;
        $this->TradeData['ESUNWALLET'] = $paymentMethod['ESUNWALLET'] ? 1 : 0;
        $this->TradeData['TAIWANPAY'] = $paymentMethod['TAIWANPAY'] ? 1 : 0;
        $this->TradeData['EZPAY'] = $paymentMethod['EZPAY'] ? 1 : 0;
        $this->TradeData['EZPWECHAT'] = $paymentMethod['EZPWECHAT'] ? 1 : 0;
        $this->TradeData['EZPALIPAY'] = $paymentMethod['EZPALIPAY'] ? 1 : 0;

        return $this;
    }

    /**
     * 付款方式-物流啟用
     *
     * 1 = 啟用超商取貨不付款
     * 2 = 啟用超商取貨付款
     * 3 = 啟用超商取貨不付款及超商取貨付款
     * null = 不開啟
     *
     * @param  int|null  $cvscom
     * @return $this
     */
    public function setCVSCOM($cvscom = null)
    {
        $this->TradeData['CVSCOM'] = $cvscom !== null ? $cvscom : $this->config->get('newebpay.CVSCOM');

        return $this;
    }

    /**
     * Set TokenTerm.
     *
     * @param  string  $token
     * @return $this
     */
    public function setTokenTerm($token = '')
    {
        $this->TradeData['TokenTerm'] = $token;

        return $this;
    }

    /**
     * Set Order.
     *
     * @param  string  $no
     * @param  int  $amt
     * @param  string  $desc
     * @param  string  $email
     * @return $this
     */
    public function setOrder($no, $amt, $desc, $email)
    {
        $this->TradeData['MerchantOrderNo'] = $no;
        $this->TradeData['Amt'] = $amt;
        $this->TradeData['ItemDesc'] = $desc;
        $this->TradeData['Email'] = $email;

        return $this;
    }
}
