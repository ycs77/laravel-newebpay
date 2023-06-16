<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Ycs77\NewebPay\Results\CustomerResult;
use Ycs77\NewebPay\Results\MPGResult;

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
        $newebPay->order($no, $amt, $desc, $email);

        return $newebPay;
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
        $newebPay->query($no, $amt);

        return $newebPay;
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
        $newebPay->cancelOrder($no, $amt, $type);

        return $newebPay;
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

        return $newebPay
            ->closeOrder($no, $amt, $type);
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
    public function cancelRequesRefund(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        return $this
            ->close($no, $amt, $type)
            ->refund()
            ->cancel();
    }
}
