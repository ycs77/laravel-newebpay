<?php

namespace Ycs77\NewebPay;

class NewebPayClose extends NewebPayRequest
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
        $this->postData['Version'] = $this->config->get('newebpay.credit_close_version');
        $this->postData['RespondType'] = 'JSON';

        $this->apiPath('/API/CreditCard/Close');
    }

    /**
     * 設定請退款的訂單內容
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * 'order' => 使用商店訂單編號追蹤
     *                        * 'trade' => 使用藍新金流交易序號追蹤
     */
    public function closeOrder(string $no, int $amt, string $type = 'order')
    {
        if ($type === 'order') {
            $this->postData['MerchantOrderNo'] = $no;
            $this->postData['IndexType'] = 1;
        } elseif ($type === 'trade') {
            $this->postData['TradeNo'] = $no;
            $this->postData['IndexType'] = 2;
        }

        $this->postData['Amt'] = $amt;

        return $this;
    }

    /**
     * 設定請款
     */
    public function pay()
    {
        return $this->closeType('pay');
    }

    /**
     * 設定退款
     */
    public function refund()
    {
        return $this->closeType('refund');
    }

    /**
     * 設定請款或退款
     *
     * @param  string  $type  類型
     *                        'pay': 請款
     *                        'refund': 退款
     */
    public function closeType(string $type)
    {
        if ($type === 'pay') {
            $this->postData['CloseType'] = 1;
        } elseif ($type === 'refund') {
            $this->postData['CloseType'] = 2;
        }

        return $this;
    }

    /**
     * 取消請款或退款
     */
    public function cancel(bool $isCancel = true)
    {
        if ($isCancel) {
            $this->postData['Cancel'] = 1;
        }

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
}
