<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\PeriodStartType;
use Ycs77\NewebPay\Enums\PeriodType;

class NewebPayPeriod extends NewebPayRequest
{
    use Concerns\WithSessionIdKey;

    /**
     * The newebpay post data.
     */
    protected array $postData = [];

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setFrontendSender();

        $this->postData['TimeStamp'] = $this->timestamp;
        $this->postData['Version'] = $this->config->get('newebpay.version.period');
        $this->postData['RespondType'] = 'JSON';

        $this->apiPath('/MPG/period');
        $this->lang();
        $this->returnUrl();
        $this->notifyUrl();
        $this->backUrl();
        $this->emailModify();
        $this->paymentInfo();
        $this->orderInfo();
        $this->unionPay();
        $this->periodStartType();
    }

    /**
     * 語系
     *
     * 語系可設定 "zh-Tw", "en"。
     */
    public function lang(string $lang = null)
    {
        $lang = $lang ?? $this->config->get('newebpay.lang');

        if (is_string($lang) && strtolower($lang) !== 'zh-tw') {
            $this->postData['LangType'] = $lang;
        }

        return $this;
    }

    /**
     * 首次付款完成後返回商店網址
     *
     * 1. 當付款人首次執行信用卡授權交易完成後，以 Form Post 方式導回商店頁。
     * 2. 若此欄位為空值，交易完成後，付款人將停留在藍新金流交易完成頁面。
     */
    public function returnUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.period.return_url')) {
            $this->postData['ReturnURL'] = $this->formatCallbackUrl($url);

            $this->WithSessionIdKey('ReturnURL');
        }

        return $this;
    }

    /**
     * 每期授權結果通知網址
     *
     * 1. 當付款人每期執行信用卡授權交易完成後，以幕後 Post 方式通知商店授權結果。
     * 2. 若此欄位為空值，則不通知商店授權結果。
     */
    public function notifyUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.period.notify_url')) {
            $this->postData['NotifyURL'] = $this->formatCallbackUrl($url);
        }

        return $this;
    }

    /**
     * 取消交易時返回商店的網址
     */
    public function backUrl(string $url = null)
    {
        if ($url = $url ?? $this->config->get('newebpay.period.back_url')) {
            $this->postData['BackURL'] = $this->formatCallbackUrl($url);
        }

        return $this;
    }

    /**
     * 付款人電子信箱是否開放修改
     */
    public function emailModify(bool $isModify = null)
    {
        if (! ($isModify ?? $this->config->get('newebpay.email_modify'))) {
            $this->postData['EmailModify'] = 0;
        }

        return $this;
    }

    /**
     * 是否開啟付款人資訊
     *
     * * 於付款人填寫此委託時，是否需顯示付款人資訊填寫欄位。
     * * 付款人資訊填寫欄位包含付款人姓名、付款人電話、付款人手機。
     */
    public function paymentInfo(bool $show = null)
    {
        if (! ($show ?? $this->config->get('newebpay.period.payment_info'))) {
            $this->postData['PaymentInfo'] = 'N';
        }

        return $this;
    }

    /**
     * 是否開啟收件人資訊
     *
     * * 於付款人填寫此委託時，是否需顯示收件人資訊填寫欄位。
     * * 收件人資訊填寫欄位包含收件人姓名、收件人電話、收件人手機、收件人地址。
     */
    public function orderInfo(bool $show = null)
    {
        if (! ($show ?? $this->config->get('newebpay.period.order_info'))) {
            $this->postData['OrderInfo'] = 'N';
        }

        return $this;
    }

    /**
     * 設定是否啟用銀聯卡支付方式
     *
     * * 銀聯卡僅支援幕後非 3D 交易
     */
    public function unionPay(bool $enabled = null)
    {
        if ($enabled) {
            $this->postData['UNIONPAY'] = 1;
        }

        return $this;
    }

    /**
     * 建立信用卡定期定額委託
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  委託金額
     * @param  string  $desc  產品名稱
     * @param  string  $email  聯絡信箱
     */
    public function periodOrder(string $no, int $amt, string $desc, string $email)
    {
        $this->postData['MerOrderNo'] = $no;
        $this->postData['PeriodAmt'] = $amt;
        $this->postData['ProdDesc'] = $desc;
        $this->postData['PayerEmail'] = $email;

        return $this;
    }

    /**
     * 設定此委託於週期間，執行信用卡授權交易的時間點
     */
    public function periodType(PeriodType $type, string $point)
    {
        $this->postData['PeriodType'] = $type->value;
        $this->postData['PeriodPoint'] = $point;

        return $this;
    }

    /**
     * 設定此委託於固定天期制觸發
     *
     * @param  int  $day  執行委託的間隔天數
     *                    * 為數字 2~999，以授權日期隔日起算。
     *                    * 例：數值為 2，則表示每隔兩天會執行一次委託
     */
    public function everyFewDays(int $day)
    {
        $this->periodType(PeriodType::EVERY_FEW_DAYS, (string) $day);

        return $this;
    }

    /**
     * 設定此委託於每週觸發
     *
     * @param  int  $weekday  在週幾執行委託
     *                        * 為數字 1 ~ 7，代表每週一至週日。
     *                        * 例：每週日執行授權，則此欄位值為 7；若週日與週一皆需執行授權，請分別建立 2 張委託
     */
    public function weekly(int $weekday)
    {
        $this->periodType(PeriodType::WEEKLY, (string) $weekday);

        return $this;
    }

    /**
     * 設定此委託於每月觸發
     *
     * @param  int  $day  在每月的第幾天執行委託
     *                    * 為數字 1 ~ 31，代表每月 1 號 ~ 31 號。若當月沒該日期則由該月的最後一天做為扣款日
     *                    * 例：每月1號執行授權，則此欄位值為1；若於1個月內需授權多次，請以建立多次委託方式執行。
     */
    public function monthly(int $day)
    {
        $this->periodType(PeriodType::MONTHLY, str_pad((string) $day, 2, '0', STR_PAD_LEFT));

        return $this;
    }

    /**
     * 設定此委託於每年觸發
     *
     * 若於 1 年內需授權多次，請以建立多次委託方式執行
     */
    public function yearly(int $month, int $day)
    {
        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
        $this->periodType(PeriodType::YEARLY, $month.$day);

        return $this;
    }

    /**
     * 授權期數
     *
     * @param  int  $times  授權委託的期數
     *                      * 為數字 1~99
     */
    public function times(int $times)
    {
        $this->postData['PeriodTimes'] = $times;

        return $this;
    }

    /**
     * 交易模式
     *
     * 委託成立後，是否立即進行信用卡授權交易，作為檢查信用卡之有效性
     *
     * * PeriodStartType::TEN_DOLLARS_NOW  立即執行十元授權
     * * PeriodStartType::AUTHORIZE_NOW    立即執行委託金額授權
     * * PeriodStartType::NO_AUTHORIZE     不檢查信用卡資訊，不授權
     */
    public function periodStartType(PeriodStartType $startType = null)
    {
        $startType = $startType ?? $this->config->get('newebpay.period.start_type');

        $this->postData['PeriodStartType'] = $startType->value;

        return $this;
    }

    /**
     * 首期授權日
     */
    public function firstdate(int $year, int $month, int $day)
    {
        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
        $this->postData['PeriodFirstdate'] = $year.'/'.$month.'/'.$day;

        return $this;
    }

    /**
     * 委託備註說明
     */
    public function memo(string $memo)
    {
        $this->postData['PeriodMemo'] = $memo;

        return $this;
    }

    /**
     * Get the newebpay post data.
     */
    public function postData(): array
    {
        return $this->postData;
    }

    protected function dataForWithSessionId(array $data = null): array
    {
        if ($data) {
            $this->postData = $data;
        }

        return $this->postData;
    }

    /**
     * Get request data.
     */
    public function requestData(): array
    {
        $postData = $this->encryptDataByAES($this->postData, $this->hashKey, $this->hashIV);

        return [
            'MerchantID_' => $this->merchantID,
            'PostData_' => $postData,
        ];
    }
}
