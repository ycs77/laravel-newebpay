<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Results\CustomerResult;
use Ycs77\NewebPay\Results\MPGResult;
use Ycs77\NewebPay\Results\PeriodNotifyResult;
use Ycs77\NewebPay\Results\PeriodResult;

class Factory
{
    /**
     * Create a new newebpay factory instance.
     */
    public function __construct(
        protected Config $config,
        protected Session $session
    ) {
    }

    /**
     * MPG 交易
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $desc  商品描述
     * @param  string  $email  聯絡信箱
     */
    public function payment(string $no, int $amt, string $desc, string $email): NewebPayMPG
    {
        $newebPay = new NewebPayMPG($this->config, $this->session);

        return $newebPay->order($no, $amt, $desc, $email);
    }

    /**
     * MPG 交易回應結果
     */
    public function result(Request $request): MPGResult
    {
        $result = new NewebPayResult($this->config, $this->session);

        return $result->result($request);
    }

    /**
     * MPG 付款取號
     *
     * 適用交易類別：超商代碼、超商條碼、超商取貨付款、ATM
     */
    public function customer(Request $request): CustomerResult
    {
        $result = new NewebPayCustomer($this->config, $this->session);

        return $result->result($request);
    }

    /**
     * 單筆交易查詢
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     */
    public function query(string $no, int $amt): NewebPayQuery
    {
        $newebPay = new NewebPayQuery($this->config, $this->session);

        return $newebPay->query($no, $amt);
    }

    /**
     * 取消信用卡授權
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function cancel(string $no, int $amt, string $type = 'order'): NewebPayCancel
    {
        $newebPay = new NewebPayCancel($this->config, $this->session);

        return $newebPay->cancelOrder($no, $amt, $type);
    }

    /**
     * 信用卡請/退款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function close(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        $newebPay = new NewebPayClose($this->config, $this->session);

        return $newebPay->closeOrder($no, $amt, $type);
    }

    /**
     * 信用卡請款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function request(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        return $this
            ->close($no, $amt, $type)
            ->pay();
    }

    /**
     * 取消信用卡請款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function cancelRequest(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        return $this
            ->close($no, $amt, $type)
            ->pay()
            ->cancel();
    }

    /**
     * 信用卡退款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function refund(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        return $this
            ->close($no, $amt, $type)
            ->refund();
    }

    /**
     * 取消信用卡退款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * **order**: 使用商店訂單編號追蹤
     *                        * **trade**: 使用藍新金流交易序號追蹤
     */
    public function cancelRefund(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        return $this
            ->close($no, $amt, $type)
            ->refund()
            ->cancel();
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
