<?php

namespace Ycs77\NewebPay;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Throwable;
use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException;

class Factory
{
    use Concerns\HasEncryption;

    /**
     * Create a new newebpay factory instance.
     */
    public function __construct(
        protected Config $config,
        protected Session $session,
        protected UserSource $userSource
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->hashKey = $this->config->get('newebpay.hash_key');
        $this->hashIV = $this->config->get('newebpay.hash_iv');
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
        $newebPay = new NewebPayMPG($this->config, $this->session, $this->userSource);
        $newebPay->order($no, $amt, $desc, $email);

        return $newebPay;
    }

    /**
     * 單筆交易查詢
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     */
    public function query(string $no, int $amt): NewebPayQuery
    {
        $newebPay = new NewebPayQuery($this->config, $this->session, $this->userSource);
        $newebPay->query($no, $amt);

        return $newebPay;
    }

    /**
     * 取消授權
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        'order' => 使用商店訂單編號追蹤
     *                        'trade' => 使用藍新金流交易序號追蹤
     */
    public function creditCancel(string $no, int $amt, string $type = 'order'): NewebPayCancel
    {
        $newebPay = new NewebPayCancel($this->config, $this->session, $this->userSource);
        $newebPay->cancelOrder($no, $amt, $type);

        return $newebPay;
    }

    /**
     * 請款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        'order' => 使用商店訂單編號追蹤
     *                        'trade' => 使用藍新金流交易序號追蹤
     */
    public function requestPayment(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        $newebPay = new NewebPayClose($this->config, $this->session, $this->userSource);
        $newebPay->closeOrder($no, $amt, $type);
        $newebPay->closeType('pay');

        return $newebPay;
    }

    /**
     * 退款
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        'order' => 使用商店訂單編號追蹤
     *                        'trade' => 使用藍新金流交易序號追蹤
     */
    public function requestRefund(string $no, int $amt, string $type = 'order'): NewebPayClose
    {
        $newebPay = new NewebPayClose($this->config, $this->session, $this->userSource);
        $newebPay->closeOrder($no, $amt, $type);
        $newebPay->closeType('refund');

        return $newebPay;
    }

    /**
     * 解碼加密字串
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function decode(string $encryptString): mixed
    {
        try {
            $decryptString = $this->decryptDataByAES($encryptString, $this->hashKey, $this->hashIV);

            return json_decode($decryptString, true);
        } catch (Throwable $e) {
            throw new NewebpayDecodeFailException($e, $encryptString);
        }
    }

    /**
     * 從 request 取得解碼加密字串
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function decodeFromRequest(HttpRequest $request = null): mixed
    {
        $request = $request ?? Request::instance();

        return $this->decode($request->input('TradeInfo'));
    }
}
