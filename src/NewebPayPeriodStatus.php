<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Results\PeriodStatusResult;

class NewebPayPeriodStatus extends NewebPayRequest
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
        $this->postData['Version'] = $this->config->get('newebpay.version.period_status');
        $this->postData['RespondType'] = 'JSON';

        $this->apiPath('/MPG/period/AlterStatus');
    }

    /**
     * 修改定期定額委託狀態
     *
     * @param  string  $no  訂單編號
     * @param  string  $periodNo  委託單號
     * @param  \Ycs77\NewebPay\Enums\PeriodStatus  $status  委託狀態
     *                                                      1. 終止委託後無法再次啟用
     *                                                      2. 暫停後再次啟用的委託將於最近一期開始授權
     *                                                      3. 委託暫停後再啟用總期數不變，扣款時間將向後展延至期數滿期
     */
    public function alterStatus(string $no, string $periodNo, PeriodStatus $status)
    {
        $this->postData['MerOrderNo'] = $no;
        $this->postData['PeriodNo'] = $periodNo;
        $this->postData['AlterType'] = $status->value;

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
    public function submit(): PeriodStatusResult
    {
        return new PeriodStatusResult($this->decode(parent::submit()['period']));
    }
}
