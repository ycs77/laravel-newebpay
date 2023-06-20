<?php

namespace Ycs77\NewebPay;

use Illuminate\Support\Carbon;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;

class NewebPayMPG extends NewebPayRequest
{
    use Concerns\WithSessionIdKey;

    /**
     * The newebpay trade data.
     */
    protected array $tradeData = [];

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setFrontendSender();

        $this->tradeData['MerchantID'] = $this->merchantID;
        $this->tradeData['TimeStamp'] = $this->timestamp;
        $this->tradeData['Version'] = $this->config->get('newebpay.version.mpg');
        $this->tradeData['RespondType'] = 'JSON';

        $this->apiPath('/MPG/mpg_gateway');
        $this->lang();
        $this->tradeLimit();
        $this->expireDate();
        $this->returnUrl();
        $this->notifyUrl();
        $this->customerUrl();
        $this->clientBackUrl();
        $this->emailModify();
        $this->loginType();
        $this->orderComment();
        $this->paymentMethods();
        $this->cvscom();
        $this->lgsType();
    }

    /**
     * 語系
     *
     * 語系可設定 "zh-tw", "en", "jp"。
     */
    public function lang(string $lang = null)
    {
        $this->tradeData['LangType'] = $lang ?? $this->config->get('newebpay.lang');

        return $this;
    }

    /**
     * 交易秒數限制
     *
     * * **0**: 不限制
     * * 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算。
     * * 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算。
     */
    public function tradeLimit(int $limit = null)
    {
        $this->tradeData['TradeLimit'] = $limit !== null
            ? $limit
            : $this->config->get('newebpay.trade_limit');

        return $this;
    }

    /**
     * 繳費有效期限
     *
     * 預設值為 7 天，上限為 180 天。
     */
    public function expireDate(int $day = null)
    {
        $day = $day !== null ? $day : $this->config->get('newebpay.expire_date');

        $this->tradeData['ExpireDate'] = Carbon::now()->addDays($day)->format('Ymd');

        return $this;
    }

    /**
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 或 443。
     */
    public function returnUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.return_url')) {
            $this->tradeData['ReturnURL'] = $this->WithSessionIdKey(
                $this->formatCallbackUrl($url)
            );
        }

        return $this;
    }

    /**
     * 付款完成後的通知連結
     *
     * 1. 以幕後方式回傳給商店相關支付結果資料
     * 2. 僅接受 port 80 或 443。
     */
    public function notifyUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.notify_url')) {
            $this->tradeData['NotifyURL'] = $this->formatCallbackUrl($url);
        }

        return $this;
    }

    /**
     * 商店取號網址
     *
     * 如果設定為 null，則會顯示取號結果在藍新金流頁面。
     */
    public function customerUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.customer_url')) {
            $this->tradeData['CustomerURL'] = $this->WithSessionIdKey(
                $this->formatCallbackUrl($url)
            );
        }

        return $this;
    }

    /**
     * 付款時點擊「返回按鈕」的網址
     *
     * 當交易中平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面。
     */
    public function clientBackUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.client_back_url')) {
            $this->tradeData['ClientBackURL'] = $this->formatCallbackUrl($url);
        }

        return $this;
    }

    /**
     * 付款人電子信箱是否開放修改
     */
    public function emailModify(bool $isModify = null)
    {
        if (! ($isModify ?? $this->config->get('newebpay.email_modify'))) {
            $this->tradeData['EmailModify'] = 0;
        }

        return $this;
    }

    /**
     * 是否需要登入藍新金流會員
     */
    public function loginType(bool $isLogin = false)
    {
        if ($isLogin ?? $this->config->get('newebpay.login_type')) {
            $this->tradeData['LoginType'] = 1;
        }

        return $this;
    }

    /**
     * 商店備註
     *
     * 1. 商店備註限制長度為 300 字。
     * 2. 若有輸入此參數，將會於 MPG 頁面呈現商店備註內容。
     */
    public function orderComment(string $comment = null)
    {
        $this->tradeData['OrderComment'] = $comment !== null
            ? $comment
            : $this->config->get('newebpay.order_comment');

        return $this;
    }

    /**
     * 設定商店需要使用的支付方式
     */
    public function paymentMethods(array $paymentMethods = [])
    {
        $paymentMethods = array_merge($this->config->get('newebpay.payment_methods'), $paymentMethods);

        if ($paymentMethods['credit']['enabled']) {
            $this->tradeData['CREDIT'] = 1;

            if ($paymentMethods['credit']['red']) {
                $this->tradeData['CreditRed'] = 1;
            }

            if ($paymentMethods['credit']['inst'] instanceof CreditInst &&
                $paymentMethods['credit']['inst'] !== CreditInst::NONE ||
                is_array($paymentMethods['credit']['inst'])
            ) {
                $this->tradeData['InstFlag'] = collect($paymentMethods['credit']['inst'])
                    ->map(fn (CreditInst $inst) => $inst->value)
                    ->join(',');
            } elseif (is_numeric($paymentMethods['credit']['inst']) || is_string($paymentMethods['credit']['inst'])) {
                $this->tradeData['InstFlag'] = $paymentMethods['credit']['inst'];
            }
        }
        if ($paymentMethods['webATM']) {
            $this->tradeData['WEBATM'] = 1;
        }
        if ($paymentMethods['VACC']) {
            $this->tradeData['VACC'] = 1;
        }
        if ($paymentMethods['bank'] instanceof Bank &&
            $paymentMethods['bank'] !== Bank::ALL ||
            is_array($paymentMethods['bank'])
        ) {
            $this->tradeData['BankType'] = collect($paymentMethods['bank'])
                    ->map(fn (Bank $inst) => $inst->value)
                    ->join(',');
        } elseif (is_string($paymentMethods['bank'])) {
            $this->tradeData['BankType'] = $paymentMethods['bank'];
        }
        if ($paymentMethods['NTCB']['enabled']) {
            $this->tradeData['NTCB'] = 1;
            /** @see \Ycs77\NewebPay\Enums\NTCBLocate */
            $this->tradeData['NTCBLocate'] = $paymentMethods['NTCB']['locate']->value;
            $this->tradeData['NTCBStartDate'] = $paymentMethods['NTCB']['start_date'];
            $this->tradeData['NTCBEndDate'] = $paymentMethods['NTCB']['end_date'];
        }

        if ($paymentMethods['googlePay']) {
            $this->tradeData['ANDROIDPAY'] = 1;
        }
        if ($paymentMethods['samsungPay']) {
            $this->tradeData['SAMSUNGPAY'] = 1;
        }
        if (is_array($paymentMethods['linePay']) && $paymentMethods['linePay']['enabled'] ||
            $paymentMethods['linePay'] === true
        ) {
            $this->tradeData['LINEPAY'] = 1;
            if (isset($paymentMethods['linePay']['image_url'])) {
                $this->tradeData['ImageUrl'] = $paymentMethods['linePay']['image_url'];
            }
        }
        if ($paymentMethods['unionPay']) {
            $this->tradeData['UNIONPAY'] = 1;
        }
        if ($paymentMethods['esunWallet']) {
            $this->tradeData['ESUNWALLET'] = 1;
        }
        if ($paymentMethods['taiwanPay']) {
            $this->tradeData['TAIWANPAY'] = 1;
        }
        if ($paymentMethods['ezPay']) {
            $this->tradeData['EZPAY'] = 1;
        }
        if ($paymentMethods['ezpWeChat']) {
            $this->tradeData['EZPWECHAT'] = 1;
        }
        if ($paymentMethods['ezpAlipay']) {
            $this->tradeData['EZPALIPAY'] = 1;
        }

        if ($paymentMethods['CVS']) {
            $this->tradeData['CVS'] = 1;
        }
        if ($paymentMethods['barcode']) {
            $this->tradeData['BARCODE'] = 1;
        }

        return $this;
    }

    /**
     * 信用卡記憶卡號
     *
     * @param string $identifier
     * * 可對應付款人之資料，用於綁定付款人與信用卡卡號時使用
     * * 例：會員編號、Email。
     * * 限英、數字，「.」、「_」、「@」、「-」格式。
     * @param \Ycs77\NewebPay\Enums\CreditRememberDemand $demand 指定付款人信用卡快速結帳必填欄位
     * * **CreditRememberDemand::EXPIRATION_DATE_AND_CVC**  必填信用卡到期日與背面末三碼
     * * **CreditRememberDemand::EXPIRATION_DATE**          必填信用卡到期日
     * * **CreditRememberDemand::CVC**                      必填背面末三碼
     */
    public function creditRemember(string $identifier, CreditRememberDemand $demand = null)
    {
        $creditRemember = $this->config->get('newebpay.payment_methods.credit_remember');

        if ($creditRemember['enabled']) {
            $this->tradeData['TokenTerm'] = $identifier;
            $this->tradeData['TokenTermDemand'] = $demand
                ? $demand->value
                : $creditRemember['demand']->value;
        }

        return $this;
    }

    /**
     * 物流搭配付款方式
     *
     * @param \Ycs77\NewebPay\Enums\CVSCOM $cvscom
     * * **CVSCOM::NOT_PAY**          啟用超商取貨不付款
     * * **CVSCOM::PAY**              啟用超商取貨付款
     * * **CVSCOM::NOT_PAY_AND_PAY**  啟用超商取貨不付款 及 超商取貨付款
     * * **CVSCOM::NONE**             不開啟
     */
    public function cvscom(CVSCOM $cvscom = null)
    {
        $cvscom = $cvscom ?? $this->config->get('newebpay.CVSCOM');

        if ($cvscom !== CVSCOM::NONE) {
            $this->tradeData['CVSCOM'] = $cvscom->value;
        }

        return $this;
    }

    /**
     * 物流型態
     *
     * @param \Ycs77\NewebPay\Enums\LgsType $lgsType
     * * **LgsType::B2C**      超商大宗寄倉(目前僅支援統㇐超商)
     * * **LgsType::C2C**      超商店到店(目前僅支援全家)
     * * **LgsType::DEFAULT**  預設
     *
     * 預設值情況說明：
     * 1. 系統優先啟用［B2C 大宗寄倉］。
     * 2. 若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
     * 3. 若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
     */
    public function lgsType(LgsType $lgsType = null)
    {
        $lgsType = $lgsType ?? $this->config->get('newebpay.lgs_type');

        if ($lgsType !== LgsType::DEFAULT) {
            $this->tradeData['LgsType'] = $lgsType->value;
        }

        return $this;
    }

    /**
     * Set the order detail data.
     */
    public function order(string $no, int $amt, string $desc, string $email)
    {
        $this->tradeData['MerchantOrderNo'] = $no;
        $this->tradeData['Amt'] = $amt;
        $this->tradeData['ItemDesc'] = $desc;
        $this->tradeData['Email'] = $email;

        return $this;
    }

    /**
     * Get the newebpay trade data.
     */
    public function tradeData(): array
    {
        return $this->tradeData;
    }

    /**
     * Get request data.
     */
    public function requestData(): array
    {
        $tradeInfo = $this->encryptDataByAES($this->tradeData, $this->hashKey, $this->hashIV);
        $tradeSha = $this->encryptDataBySHA($tradeInfo, $this->hashKey, $this->hashIV);

        return [
            'MerchantID' => $this->merchantID,
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
            'Version' => $this->tradeData['Version'],
        ];
    }
}
