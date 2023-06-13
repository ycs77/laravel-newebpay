<?php

namespace Ycs77\NewebPay;

class NewebPayClose extends BaseNewebPay
{
    /**
     * The newebpay trade data.
     */
    protected array $TradeData = [];

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->TradeData['TimeStamp'] = $this->timestamp;

        $this->setApiPath('API/CreditCard/Close');
        $this->setAsyncSender();

        $this->setVersion();
        $this->setRespondType();
        $this->setNotifyURL();
    }

    /**
     * 串接版本
     */
    public function setVersion(string $version = null)
    {
        $this->TradeData['Version'] = $version ?? $this->config->get('newebpay.version');

        return $this;
    }

    /**
     * 回傳格式
     *
     * 回傳格式可設定 "JSON" 或 "String"。
     */
    public function setRespondType(string $type = null)
    {
        $this->TradeData['RespondType'] = $type ?? $this->config->get('newebpay.respond_type');

        return $this;
    }

    /**
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     * 僅接受 port 80 或 443。
     */
    public function setNotifyURL(string $url = null)
    {
        $this->TradeData['NotifyURL'] = $url ?? $this->config->get('newebpay.notify_url');

        return $this;
    }

    /**
     * 設定請退款的模式
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        'order' => 使用商店訂單編號追蹤
     *                        'trade' => 使用藍新金流交易序號追蹤
     */
    public function setCloseOrder(string $no, int $amt, string $type = 'order')
    {
        if ($type === 'order') {
            $this->TradeData['MerchantOrderNo'] = $no;
            $this->TradeData['IndexType'] = 1;
        } elseif ($type === 'trade') {
            $this->TradeData['TradeNo'] = $no;
            $this->TradeData['IndexType'] = 2;
        }

        $this->TradeData['Amt'] = $amt;

        return $this;
    }

    /**
     * 設定請款或退款
     *
     * @param  string  $type  類型
     *                        'pay': 請款
     *                        'refund': 退款
     */
    public function setCloseType(string $type = 'pay')
    {
        if ($type === 'pay') {
            $this->TradeData['CloseType'] = 1;
        } elseif ($type === 'refund') {
            $this->TradeData['CloseType'] = 2;
        }

        return $this;
    }

    public function setCancel(bool $isCancel = false)
    {
        $this->TradeData['Cancel'] = $isCancel;

        return $this;
    }

    /**
     * Get request data.
     */
    public function getRequestData(): array
    {
        $postData = $this->encryptDataByAES($this->TradeData, $this->hashKey, $this->hashIV);

        return [
            'MerchantID_' => $this->merchantID,
            'PostData_' => $postData,
        ];
    }
}
