<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Results\PeriodAmtResult;

class NewebPayPeriodAmt extends NewebPayRequest
{
    /**
     * The newebpay post data.
     */
    protected array $postData = [];

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setBackgroundSender();

        $this->postData['TimeStamp'] = $this->timestamp;
        $this->postData['Version'] = $this->config->get('newebpay.version.period_amt');
        $this->postData['RespondType'] = 'JSON';

        $this->apiPath('/MPG/period/AlterAmt');
    }

    /**
     * 修改定期定額委託內容
     *
     * @param  string  $no  訂單編號
     * @param  string  $periodNo  委託單號
     * @param  int  $amt  委託金額
     */
    public function alter(string $no, string $periodNo, int $amt)
    {
        $this->postData['MerOrderNo'] = $no;
        $this->postData['PeriodNo'] = $periodNo;
        $this->postData['AlterAmt'] = $amt;

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
     * 調整信用卡到期日
     */
    public function creditExpiredAt(int $month, int $day)
    {
        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
        $this->postData['Extday'] = $month.$day;

        return $this;
    }

    /**
     * Get the newebpay post data.
     */
    public function postData(): array
    {
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

    /**
     * Submit data to newebpay API.
     */
    public function submit(): PeriodAmtResult
    {
        return new PeriodAmtResult($this->decode(parent::submit()['period']));
    }
}
