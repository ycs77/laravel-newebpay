<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Results\PeriodNotifyResult;
use Ycs77\NewebPay\Results\PeriodResult;

/** @deprecated */
class FactoryV1
{
    /**
     * Create a new newebpay factory instance.
     */
    public function __construct(
        protected Config $config,
        protected Session $session
    ) {
        //
    }

    /**
     * 建立信用卡定期定額委託
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  委託金額
     * @param  string  $desc  產品名稱
     * @param  string  $email  聯絡信箱
     */
    public function period(string $no, int $amt, string $desc, string $email): NewebPayPeriod
    {
        $newebPay = new NewebPayPeriod($this->config, $this->session);

        return $newebPay->periodOrder($no, $amt, $desc, $email);
    }

    /**
     * 建立定期定額委託回傳結果
     */
    public function periodResult(Request $request): PeriodResult
    {
        $result = new NewebPayPeriodResult($this->config, $this->session);

        return $result->result($request);
    }

    /**
     * 定期定額每期委託完成回傳結果
     */
    public function periodNotify(Request $request): PeriodNotifyResult
    {
        $result = new NewebPayPeriodNotify($this->config, $this->session);

        return $result->result($request);
    }

    /**
     * 修改信用卡定期定額委託狀態
     *
     * @param  string  $no  訂單編號
     * @param  string  $periodNo  委託單號
     * @param  \Ycs77\NewebPay\Enums\PeriodStatus  $status  委託狀態
     *                                                      1. 終止委託後無法再次啟用
     *                                                      2. 暫停後再次啟用的委託將於最近一期開始授權
     *                                                      3. 委託暫停後再啟用總期數不變，扣款時間將向後展延至期數滿期
     */
    public function periodStatus(string $no, string $periodNo, PeriodStatus $status): NewebPayPeriodStatus
    {
        $newebPay = new NewebPayPeriodStatus($this->config, $this->session);

        return $newebPay->alterStatus($no, $periodNo, $status);
    }

    /**
     * 修改信用卡定期定額委託內容
     *
     * @param  string  $no  訂單編號
     * @param  string  $periodNo  委託單號
     * @param  int  $amt  委託金額
     */
    public function periodAmt(string $no, string $periodNo, int $amt): NewebPayPeriodAmt
    {
        $newebPay = new NewebPayPeriodAmt($this->config, $this->session);

        return $newebPay->alter($no, $periodNo, $amt);
    }
}
