<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\RespondType;

class NewebPayCancel extends BaseNewebPay
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
        $this->postData['Version'] = $this->config->get('newebpay.credit_cancel_version');

        $this->apiPath('/API/CreditCard/Cancel');
        $this->respondType();
    }

    /**
     * 回傳格式
     *
     * 回傳格式可設定 JSON 或 String。
     */
    public function respondType(RespondType $type = null)
    {
        $this->respondType = $type
            ? $type->value
            : $this->config->get('newebpay.respond_type')->value;

        $this->postData['RespondType'] = $this->respondType;

        return $this;
    }

    /**
     * 設定取消授權的模式
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     * @param  string  $type  編號類型
     *                        * 'order' => 使用商店訂單編號追蹤
     *                        * 'trade' => 使用藍新金流交易序號追蹤
     */
    public function cancelOrder(string $no, int $amt, string $type = 'order')
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
