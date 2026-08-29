<?php

namespace Ycs77\NewebPay\Builders\Trade;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Trade\MPGOptions;
use Ycs77\NewebPay\Url\PrependAppUrl;
use Ycs77\NewebPay\Url\WithSessionIdKey;

final class MPGBuilder extends Builder
{
    private MPGOptions $options;

    public function __construct(
        Factory $factory,
        Crypto $crypto,
        HttpTransporter $httpTransporter,
        private readonly WithSessionIdKey $withSessionIdKey,
        private readonly PrependAppUrl $prependAppUrl,
        array $config
    ) {
        parent::__construct($factory, $crypto, $httpTransporter, $config);
    }

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);

        $this->options = new MPGOptions;
        $this->options->merchantId = $this->config['merchant_id'];

        $this->endpoint = '/MPG/mpg_gateway';

        if ($lang = $this->config['lang']) {
            $this->withLang($lang);
        }
    }

    public function options(): MPGOptions
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
    public function withLang(LangType $lang): self
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
        $this->options->returnURL = $this->withSessionIdKey->handle(
            $this->prependAppUrl->handle($url)
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
        $this->options->notifyURL = $this->prependAppUrl->handle($url);

        return $this;
    }

    /**
     * 商店取號網址
     *
     * 如果未設定，則會顯示取號結果在藍新金流頁面。
     */
    public function withCustomerUrl(string $url): self
    {
        $this->options->customerURL = $this->withSessionIdKey->handle(
            $this->prependAppUrl->handle($url)
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
        $this->options->clientBackURL = $this->prependAppUrl->handle($url);

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
     * 信用卡支付
     *
     * @param  bool  $enabled  是否啟用信用卡支付
     * @param  bool  $red  是否啟用紅利
     * @param  CreditInst|array  $inst  分期設定：
     *                                  - **CreditInst::NONE**  不啟用 (預設值)
     *                                  - **CreditInst::ALL**   啟用全部分期
     *                                  - **CreditInst::P3**    分 3 期
     *                                  - **CreditInst::P6**    分 6 期
     *                                  - **CreditInst::P12**   分 12 期
     *                                  - **CreditInst::P18**   分 18 期
     *                                  - **CreditInst::P24**   分 24 期
     *                                  使用陣列開啟多種分期，例如：`[CreditInst::P3, CreditInst::P6]`
     */
    public function withCredit(bool $enabled = true, bool $red = false, CreditInst|array $inst = CreditInst::NONE): self
    {
        $this->options->credit = $enabled;
        $this->options->creditRed = $red;
        $this->options->creditInstallment = $inst;

        return $this;
    }

    /**
     * 信用卡記憶卡號
     *
     * - **CreditRememberDemand::EXPIRATION_DATE_AND_CVC** 必填信用卡到期日與背面末三碼 (預設值)
     * - **CreditRememberDemand::EXPIRATION_DATE**         必填信用卡到期日
     * - **CreditRememberDemand::CVC**                     必填背面末三碼
     *
     * @param  string  $identifier  付款人綁定資料，用於綁定付款人與信用卡卡號時使用。例：會員編號、Email。格式限英、數字、「.」、「_」、「@」、「-」。
     * @param  CreditRememberDemand  $demand  指定付款人信用卡快速結帳必填欄位設定
     */
    public function withCreditRemember(string $identifier, ?CreditRememberDemand $demand = CreditRememberDemand::EXPIRATION_DATE_AND_CVC): self
    {
        $this->options->creditRememberIdentifier = $identifier;
        $this->options->creditRememberDemand = $demand;

        return $this;
    }

    /**
     * WebATM 支付
     */
    public function withWebAtm(bool $enabled = true): self
    {
        $this->options->webAtm = $enabled;

        return $this;
    }

    /**
     * ATM 轉帳
     */
    public function withAtmTransfer(bool $enabled = true): self
    {
        $this->options->atmTransfer = $enabled;

        return $this;
    }

    /**
     * 轉帳銀行
     *
     * WebATM 與 ATM 轉帳可供付款人選擇轉帳銀行，將顯示於 MPG 頁上。為共用此參數值，無法個別分開指定。
     *
     * - **Bank::BOT**        台灣銀行
     * - **Bank::HNCB**       華南銀行
     * - **Bank::FirstBank**  第一銀行
     *
     * 使用陣列指定 1 個以上的銀行，例如：`[Bank::BOT, Bank::HNCB]`。
     *
     * 若未設定此參數，則預設會顯示所有銀行選項。
     *
     * 每日的 00:00:00-01:00:00 為第一銀行例行維護時間，在此時間區間內，將不會顯示
     * ［第一銀行］的選項，若商店在此時間區間僅指定第一銀行一家銀行，將會回應
     * ［MPG01027］的錯誤代碼。
     *
     * @param  Bank|array  $bank  轉帳銀行
     */
    public function withBank(Bank|array $bank): self
    {
        $this->options->bank = $bank;

        return $this;
    }

    /**
     * 信用卡 國民旅遊卡
     *
     * @param  NTCBLocate  $locate  旅遊地區，可使用地區請參考 `\Ycs77\NewebPay\Enums\NTCBLocate` 類別
     * @param  string  $startDate  國民旅遊卡起始日期
     * @param  string  $endDate  國民旅遊卡結束日期
     */
    public function withNationalTravelCard(NTCBLocate $locate, string $startDate, string $endDate): self
    {
        $this->options->nationalTravelCard = true;
        $this->options->nationalTravelCardLocate = $locate;
        $this->options->nationalTravelCardStartDate = $startDate;
        $this->options->nationalTravelCardEndDate = $endDate;

        return $this;
    }

    /**
     * Google Pay
     */
    public function withGooglePay(bool $enabled = true): self
    {
        $this->options->googlePay = $enabled;

        return $this;
    }

    /**
     * Samsung Pay
     */
    public function withSamsungPay(bool $enabled = true): self
    {
        $this->options->samsungPay = $enabled;

        return $this;
    }

    /**
     * LINE Pay
     *
     * @param  bool  $enabled  是否啟用 LINE Pay 支付
     * @param  string|null  $imageUrl  產品圖檔連結網址。此連結的圖檔將顯示於 LINE Pay 付款前的產品圖片區，
     *                                 若無產品圖檔連結網址，會使用藍新系統預設圖檔。圖片尺寸建議使用 84*84 像素。
     */
    public function withLinePay(bool $enabled = true, ?string $imageUrl = null): self
    {
        $this->options->linePay = $enabled;
        $this->options->linePayImageUrl = $imageUrl;

        return $this;
    }

    /**
     * 銀聯卡支付
     */
    public function withUnionPay(bool $enabled = true): self
    {
        $this->options->unionPay = $enabled;

        return $this;
    }

    /**
     * 玉山 Wallet
     */
    public function withEsunWallet(bool $enabled = true): self
    {
        $this->options->esunWallet = $enabled;

        return $this;
    }

    /**
     * 台灣 Pay
     */
    public function withTaiwanPay(bool $enabled = true): self
    {
        $this->options->taiwanPay = $enabled;

        return $this;
    }

    /**
     * 簡單付電子錢包
     */
    public function withEzPay(bool $enabled = true): self
    {
        $this->options->ezPay = $enabled;

        return $this;
    }

    /**
     * 簡單付微信支付
     */
    public function withEzPayWeChat(bool $enabled = true): self
    {
        $this->options->ezPayWeChat = $enabled;

        return $this;
    }

    /**
     * 簡單付支付寶
     */
    public function withEzPayAlipay(bool $enabled = true): self
    {
        $this->options->ezPayAlipay = $enabled;

        return $this;
    }

    /**
     * 超商代碼繳費支付
     */
    public function withCvsCode(bool $enabled = true): self
    {
        $this->options->cvsCode = $enabled;

        return $this;
    }

    /**
     * 條碼繳費支付
     */
    public function withBarcode(bool $enabled = true): self
    {
        $this->options->barcode = $enabled;

        return $this;
    }

    /**
     * 物流搭配付款方式
     *
     * - **CVSCOM::NOT_PAY**         啟用超商取貨不付款
     * - **CVSCOM::PAY**             啟用超商取貨付款
     * - **CVSCOM::NOT_PAY_AND_PAY** 啟用超商取貨不付款 及 超商取貨付款
     * - **CVSCOM::NONE**            不開啟
     */
    public function withLogisticsPayment(CVSCOM $cvscom): self
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
    public function withLogisticsType(LgsType $lgsType): self
    {
        $this->options->lgsType = $lgsType;

        return $this;
    }

    public function submit(): Response
    {
        return $this->sendFormRedirectRequest();
    }
}
