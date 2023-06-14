<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\RespondType;

class NewebPayCancel extends BaseNewebPay
{
    /**
     * The newebpay trade data.
     */
    protected array $tradeData = [];

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->tradeData['TimeStamp'] = $this->timestamp;
        $this->tradeData['Version'] = $this->config->get('newebpay.credit_cancel_version');

        $this->setApiPath('/API/CreditCard/Cancel');
        $this->setBackgroundSender();

        $this->setRespondType();
        $this->setNotifyURL();
    }

    /**
     * 回傳格式
     *
     * 回傳格式可設定 JSON 或 String。
     */
    public function setRespondType(RespondType $type = null)
    {
        $this->respondType = $type
            ? $type->value
            : $this->config->get('newebpay.respond_type')->value;

        $this->tradeData['RespondType'] = $this->respondType;

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
        $this->tradeData['NotifyURL'] = $this->config->get('app.url').($url ?? $this->config->get('newebpay.notify_url'));

        return $this;
    }

    /**
     * 設定取消授權的模式
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        'order' => 使用商店訂單編號追蹤
     *                        'trade' => 使用藍新金流交易序號追蹤
     */
    public function setCancelOrder(string $no, int $amt, string $type = 'order')
    {
        if ($type === 'order') {
            $this->tradeData['MerchantOrderNo'] = $no;
            $this->tradeData['IndexType'] = 1;
        } elseif ($type === 'trade') {
            $this->tradeData['TradeNo'] = $no;
            $this->tradeData['IndexType'] = 2;
        }

        $this->tradeData['Amt'] = $amt;

        return $this;
    }

    /**
     * Get request data.
     */
    public function getRequestData(): array
    {
        $postData = $this->encryptDataByAES($this->tradeData, $this->hashKey, $this->hashIV);

        return [
            'MerchantID_' => $this->merchantID,
            'PostData_' => $postData,
        ];
    }
}
