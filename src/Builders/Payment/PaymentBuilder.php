<?php

namespace Ycs77\NewebPay\Builders\Payment;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Options\Payment\PaymentOptions;
use Ycs77\NewebPay\Url\UrlFormat;

class PaymentBuilder extends Builder
{
    protected PaymentOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));

        $this->options = new PaymentOptions;
        $this->options->merchantId = $this->factory->config('merchant_id');

        $this->endpoint = '/MPG/mpg_gateway';

        if ($lang = $this->factory->config('lang')) {
            $this->withLanguage($lang);
        }

        if ($returnUrl = $this->factory->config('return_url')) {
            $this->withReturnUrl($returnUrl);
        }

        if ($notifyUrl = $this->factory->config('notify_url')) {
            $this->withNotifyUrl($notifyUrl);
        }

        if ($customerUrl = $this->factory->config('customer_url')) {
            $this->withCustomerUrl($customerUrl);
        }

        if ($clientBackUrl = $this->factory->config('client_back_url')) {
            $this->withClientBackUrl($clientBackUrl);
        }

        if ($paymentMethods = $this->factory->config('payment_methods')) {
            $this->withPaymentMethods($paymentMethods);
        }
    }

    public function getOptions(): PaymentOptions
    {
        return $this->options;
    }

    /**
     * 語系
     *
     * - 繁體中文 (`LangType::ZH_TW`)
     * - 英文 (`LangType::EN`)
     * - 日文 (`LangType::JP`)
     *
     * 預設值為繁體中文。
     */
    public function withLanguage(LangType $lang): self
    {
        $this->options->lang = $lang;

        return $this;
    }

    /**
     * 商店自訂訂單編號
     *
     * @param  string  $orderNo  商店自訂訂單編號，限英、數字、_ 格式。同一商店中此編號不可重覆。
     */
    public function withOrder(string $orderNo): self
    {
        $this->options->orderNo = $orderNo;

        return $this;
    }

    /**
     * 訂單金額
     */
    public function withAmount(int $amount): self
    {
        $this->options->amount = $amount;

        return $this;
    }

    /**
     * 商品資訊
     *
     * @param  string  $description  商品資訊，限制長度為 50 字元。
     */
    public function withItemDescription(string $description): self
    {
        $this->options->itemDescription = $description;

        return $this;
    }

    /**
     * 交易秒數限制
     *
     * - **0**: 不限制 (預設值)
     * - 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算。
     * - 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算。
     */
    public function withTradeLimit(int $limit): self
    {
        $this->options->tradeLimit = $limit;

        return $this;
    }

    /**
     * 繳費有效期限
     *
     * 僅適用於非即時支付
     *
     * 預設值為 7 天，上限為 180 天。
     */
    public function withExpireDays(int|DateTime $days): self
    {
        $this->options->expireDate = $days instanceof DateTime
            ? $days->format('Ymd')
            : Carbon::now()->addDays($days)->format('Ymd');

        return $this;
    }

    /**
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 或 443。
     */
    public function withReturnUrl(string $url): self
    {
        $this->options->returnURL = UrlFormat::withSessionIdKey(
            UrlFormat::formatCallbackUrl($url)
        );

        return $this;
    }

    /**
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     *
     * 僅接受 port 80 或 443。
     */
    public function withNotifyUrl(string $url): self
    {
        $this->options->notifyURL = UrlFormat::formatCallbackUrl($url);

        return $this;
    }

    /**
     * 商店取號網址
     *
     * 如果未設定，則會顯示取號結果在藍新金流頁面。
     */
    public function withCustomerUrl(string $url): self
    {
        $this->options->customerURL = UrlFormat::withSessionIdKey(
            UrlFormat::formatCallbackUrl($url)
        );

        return $this;
    }

    /**
     * 付款時點擊「返回按鈕」的網址
     *
     * 當交易中平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面。
     */
    public function withClientBackUrl(string $url): self
    {
        $this->options->clientBackURL = UrlFormat::formatCallbackUrl($url);

        return $this;
    }

    /**
     * 付款人電子信箱
     *
     * 交易完成或付款完成時，通知付款人使用
     */
    public function withEmail(string $email): self
    {
        $this->options->email = $email;

        return $this;
    }

    /**
     * 付款人電子信箱是否開放修改
     *
     * 預設為可修改
     */
    public function withEmailModify(bool $isModify): self
    {
        $this->options->emailModify = $isModify;

        return $this;
    }

    /**
     * 關閉付款人電子信箱修改功能
     */
    public function disableEmailModify(): self
    {
        $this->options->emailModify = false;

        return $this;
    }

    /**
     * 商店備註
     *
     * 商店備註限制長度為 300 字。
     *
     * 若有輸入此參數，將會於 MPG 頁面呈現商店備註內容。
     */
    public function withOrderComment(string $comment): self
    {
        $this->options->orderComment = $comment;

        return $this;
    }

    /**
     * 付款方式
     *
     * @param  array  $paymentMethods  支付方式設定，詳見 `config/newebpay.php`
     */
    public function withPaymentMethods(array $paymentMethods): self
    {
        $this->options->paymentMethods = array_merge(
            $this->options->paymentMethods,
            $paymentMethods
        );

        return $this;
    }

    /**
     * 信用卡記憶卡號
     *
     * - 必填信用卡到期日與背面末三碼: **CreditRememberDemand::EXPIRATION_DATE_AND_CVC** (預設值)
     * - 必填信用卡到期日: **CreditRememberDemand::EXPIRATION_DATE**
     * - 必填背面末三碼: **CreditRememberDemand::CVC**
     *
     * @param  string  $identifier  付款人綁定資料，用於綁定付款人與信用卡卡號時使用。例：會員編號、Email。格式限英、數字，「.」、「_」、「@」、「-」。
     * @param  \Ycs77\NewebPay\Enums\CreditRememberDemand  $demand  指定付款人信用卡快速結帳必填欄位設定
     */
    public function withCreditRemember(string $identifier, ?CreditRememberDemand $demand = null): self
    {
        $this->options->creditRememberIdentifier = $identifier;
        $this->options->creditRememberDemand = $demand;

        return $this;
    }

    /**
     * 物流搭配付款方式
     *
     * - 啟用超商取貨不付款: **CVSCOM::NOT_PAY**
     * - 啟用超商取貨付款: **CVSCOM::PAY**
     * - 啟用超商取貨不付款 及 超商取貨付款: **CVSCOM::NOT_PAY_AND_PAY**
     * - 不開啟: **CVSCOM::NONE**
     */
    public function withLogisticsPayment(CVSCOM $cvscom)
    {
        $this->options->cvscom = $cvscom;

        return $this;
    }

    /**
     * 物流型態
     *
     * - **LgsType::DEFAULT**  預設
     * - **LgsType::B2C**      超商大宗寄倉(目前僅支援統㇐超商)
     * - **LgsType::C2C**      超商店到店(目前僅支援全家)
     *
     * 預設值情況說明：
     * 1. 系統優先啟用［B2C 大宗寄倉］。
     * 2. 若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
     * 3. 若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
     */
    public function withLogisticsType(LgsType $lgsType)
    {
        $this->options->lgsType = $lgsType;

        return $this;
    }

    public function toRequestData(): array
    {
        $data = parent::toRequestData();

        $data['formData']['TradeInfo'] = $this->crypto->encryptByAES(
            $data['formData']['TradeInfo']
        );

        $data['formData']['TradeSha'] = $this->crypto->hashBySHA(
            $data['formData']['TradeInfo']
        );

        return $data;
    }

    public function send(): Response
    {
        return $this->sendFormPostRequest();
    }
}
