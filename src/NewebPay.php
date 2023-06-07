<?php

namespace Ycs77\NewebPay;

use Illuminate\Support\Facades\Request;
use Throwable;
use Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException;

class NewebPay extends BaseNewebPay
{
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
        $newebPay = new NewebPayMPG($this->config);
        $newebPay->setOrder($no, $amt, $desc, $email);

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
        $newebPay = new NewebPayQuery($this->config);
        $newebPay->setQuery($no, $amt);

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
        $newebPay = new NewebPayCancel($this->config);
        $newebPay->setCancelOrder($no, $amt, $type);

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
        $newebPay = new NewebPayClose($this->config);
        $newebPay->setCloseOrder($no, $amt, $type);
        $newebPay->setCloseType('pay');

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
        $newebPay = new NewebPayClose($this->config);
        $newebPay->setCloseOrder($no, $amt, $type);
        $newebPay->setCloseType('refund');

        return $newebPay;
    }

    /**
     * 信用卡授權 - 首次交易
     *
     * @param  array  $data
     *                       $data['no'] => 訂單編號
     *                       $data['email'] => 購買者 email
     *                       $data['cardNo'] => 信用卡號
     *                       $data['exp'] => 到期日 格式: 2021/01 -> 2101
     *                       $data['cvc'] => 信用卡驗證碼 格式: 3碼
     *                       $data['desc] => 商品描述
     *                       $data['amt'] => 綁定支付金額
     *                       $data['tokenTerm'] => 約定信用卡付款之付款人綁定資料
     */
    public function creditcardFirstTrade(array $data): NewebPayCreditCard
    {
        $newebPay = new NewebPayCreditCard($this->config);
        $newebPay->firstTrade($data);

        return $newebPay;
    }

    /**
     * 信用卡授權 - 使用已綁定信用卡進行交易
     *
     * @param  array  $data
     *                       $data['no'] => 訂單編號
     *                       $data['amt'] => 訂單金額
     *                       $data['desc'] => 商品描述
     *                       $data['email'] => 購買者 email
     *                       $data['tokenValue'] => 綁定後取回的 token 值
     *                       $data['tokenTerm'] => 約定信用卡付款之付款人綁定資料 要與第一次綁定時一樣
     */
    public function creditcardTradeWithToken(array $data): NewebPayCreditCard
    {
        $newebPay = new NewebPayCreditCard($this->config);
        $newebPay->tradeWithToken($data);

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
            $decryptString = $this->decryptDataByAES($encryptString, $this->HashKey, $this->HashIV);

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
    public function decodeFromRequest(): mixed
    {
        return $this->decode(Request::input('TradeInfo'));
    }
}
