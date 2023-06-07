<?php

namespace Ycs77\NewebPay\Concerns;

use Illuminate\Support\Carbon;

trait TradeData
{
    /**
     * The newebpay trade data.
     */
    protected array $TradeData = [];

    /**
     * Bootstrap the trade data.
     */
    public function tradeDataBoot(): void
    {
        $this->TradeData['TimeStamp'] = $this->timestamp;
        $this->setVersion();
        $this->setRespondType();
    }

    public function getTradeData(): array
    {
        return $this->TradeData;
    }

    /**
     * 串接版本
     */
    public function setVersion(string $version = null): self
    {
        $this->TradeData['Version'] = $version ?? $this->config->get('newebpay.version');

        return $this;
    }

    /**
     * 回傳格式
     *
     * 回傳格式可設定 "JSON" 或 "String"。
     */
    public function setRespondType(string $type = null): self
    {
        $this->TradeData['RespondType'] = $type ?? $this->config->get('newebpay.respond_type');

        return $this;
    }

    /**
     * 語系
     *
     * 語系可設定 "zh-tw"、"en"。
     */
    public function setLangType(string $lang = null): self
    {
        $this->TradeData['LangType'] = $lang ?? $this->config->get('newebpay.lang_type');

        return $this;
    }

    /**
     * 交易秒數限制
     *
     * 0: 不限制
     * 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算。
     * 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算。
     */
    public function setTradeLimit(int $limit = null): self
    {
        $this->TradeData['TradeLimit'] = $limit !== null
            ? $limit
            : $this->config->get('newebpay.trade_limit');

        return $this;
    }

    /**
     * 繳費有效期限
     */
    public function setExpireDate(int $day = null): self
    {
        $day = $day !== null ? $day : $this->config->get('newebpay.expire_date');

        $this->TradeData['ExpireDate'] = Carbon::now()->addDays($day)->format('Ymd');

        return $this;
    }

    /**
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 或 443。
     */
    public function setReturnURL(string $url = null): self
    {
        $this->TradeData['ReturnURL'] = $url ?? $this->config->get('newebpay.return_url');

        return $this;
    }

    /**
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     * 僅接受 port 80 或 443。
     */
    public function setNotifyURL(string $url = null): self
    {
        $this->TradeData['NotifyURL'] = $url ?? $this->config->get('newebpay.notify_url');

        return $this;
    }

    /**
     * 商店取號網址
     *
     * 如果設定為 null，則會顯示取號結果在藍新金流頁面。
     */
    public function setCustomerURL(string $url = null): self
    {
        $this->TradeData['CustomerURL'] = $url ?? $this->config->get('newebpay.customer_url');

        return $this;
    }

    /**
     * 付款取消時返回商店網址
     *
     * 當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面。
     */
    public function setClientBackURL(string $url = null): self
    {
        $this->TradeData['ClientBackURL'] = $url ?? $this->config->get('newebpay.client_back_url');

        return $this;
    }

    /**
     * 付款人電子信箱是否開放修改
     */
    public function setEmailModify(bool $isModify = null): self
    {
        $this->TradeData['EmailModify'] = (
            $isModify !== null
                ? $isModify
                : $this->config->get('newebpay.email_modify')
        ) ? 1 : 0;

        return $this;
    }

    /**
     * 是否需要登入藍新金流會員
     */
    public function setLoginType(bool $isLogin = false): self
    {
        $this->TradeData['LoginType'] = (
            $isLogin !== null
                ? $isLogin
                : $this->config->get('newebpay.login_type')
        ) ? 1 : 0;

        return $this;
    }

    /**
     * 商店備註
     *
     * 1. 商店備註限制長度為 300 字。
     * 2. 若有輸入此參數，將會於 MPG 頁面呈現商店備註內容。
     */
    public function setOrderComment(string $comment = null): self
    {
        $this->TradeData['OrderComment'] = $comment !== null
            ? $comment
            : $this->config->get('newebpay.order_comment');

        return $this;
    }

    /**
     * 設定商店需要使用的支付方式
     */
    public function setPaymentMethod(array $paymentMethod = []): self
    {
        $paymentMethod = array_merge($this->config->get('newebpay.payment_method'), $paymentMethod);

        $this->TradeData['CREDIT'] = $paymentMethod['CREDIT']['Enable'] ? 1 : 0;
        $this->TradeData['ANDROIDPAY'] = $paymentMethod['ANDROIDPAY'] ? 1 : 0;
        $this->TradeData['SAMSUNGPAY'] = $paymentMethod['SAMSUNGPAY'] ? 1 : 0;
        $this->TradeData['LINEPAY'] = isset($paymentMethod['LINEPAY']) && $paymentMethod['LINEPAY'] ? 1 : 0;
        $this->TradeData['ImageUrl'] = isset($paymentMethod['ImageUrl']) && $paymentMethod['ImageUrl'] ? 1 : 0;
        $this->TradeData['InstFlag'] = ($paymentMethod['CREDIT']['Enable'] && $paymentMethod['CREDIT']['InstFlag']) ? $paymentMethod['CREDIT']['InstFlag'] : 0;
        $this->TradeData['CreditRed'] = ($paymentMethod['CREDIT']['Enable'] && $paymentMethod['CREDIT']['CreditRed']) ? 1 : 0;
        $this->TradeData['UNIONPAY'] = $paymentMethod['UNIONPAY'] ? 1 : 0;
        $this->TradeData['WEBATM'] = $paymentMethod['WEBATM'] ? 1 : 0;
        $this->TradeData['VACC'] = $paymentMethod['VACC'] ? 1 : 0;
        $this->TradeData['CVS'] = $paymentMethod['CVS'] ? 1 : 0;
        $this->TradeData['BARCODE'] = $paymentMethod['BARCODE'] ? 1 : 0;
        $this->TradeData['ESUNWALLET'] = isset($paymentMethod['ESUNWALLET']) && $paymentMethod['ESUNWALLET'] ? 1 : 0;
        $this->TradeData['TAIWANPAY'] = isset($paymentMethod['TAIWANPAY']) && $paymentMethod['TAIWANPAY'] ? 1 : 0;
        $this->TradeData['EZPAY'] = isset($paymentMethod['EZPAY']) && $paymentMethod['EZPAY'] ? 1 : 0;
        $this->TradeData['EZPWECHAT'] = isset($paymentMethod['EZPWECHAT']) && $paymentMethod['EZPWECHAT'] ? 1 : 0;
        $this->TradeData['EZPALIPAY'] = isset($paymentMethod['EZPALIPAY']) && $paymentMethod['EZPALIPAY'] ? 1 : 0;

        return $this;
    }

    /**
     * 物流搭配付款方式
     *
     * 1: 啟用超商取貨不付款
     * 2: 啟用超商取貨付款
     * 3: 啟用超商取貨不付款及超商取貨付款
     * null: 不開啟
     */
    public function setCVSCOM(int $cvscom = null): self
    {
        $this->TradeData['CVSCOM'] = $cvscom !== null ? $cvscom : $this->config->get('newebpay.CVSCOM');

        return $this;
    }

    /**
     * 物流型態
     *
     * B2C: 超商大宗寄倉(目前僅支援統㇐超商)
     * C2C: 超商店到店(目前僅支援全家)
     * null: 預設
     *
     * 預設值情況說明：
     * 1. 系統優先啟用［B2C 大宗寄倉］。
     * 2. 若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
     * 3. 若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
     */
    public function setLgsType(string $lgsType = null): self
    {
        $this->TradeData['LgsType'] = $lgsType !== null ? $lgsType : $this->config->get('newebpay.lgs_type');

        return $this;
    }

    public function setTokenTerm(string $token = ''): self
    {
        $this->TradeData['TokenTerm'] = $token;

        return $this;
    }

    public function setOrder(string $no, int $amt, string $desc, string $email): self
    {
        $this->TradeData['MerchantOrderNo'] = $no;
        $this->TradeData['Amt'] = $amt;
        $this->TradeData['ItemDesc'] = $desc;
        $this->TradeData['Email'] = $email;

        return $this;
    }
}
