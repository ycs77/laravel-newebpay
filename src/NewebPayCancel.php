<?php

namespace Webcs4JIG\NewebPay;

class NewebPayCancel extends BaseNewebPay
{
    /**
     * The newebpay boot hook.
     *
     * @return void
     */
    public function boot()
    {
        $this->setApiPath('API/CreditCard/Cancel');
        $this->setAsyncSender();

        $this->setNotifyURL();
    }

    /**
     * 設定取消授權的模式
     *
     * @param  string  $no
     * @param  int  $amt
     * @param  string  $type
     *                        'order': 使用商店訂單編號
     *                        'trade': 使用藍新金流交易序號
     * @return $this
     */
    public function setCancelOrder($no, $amt, $type = 'order')
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
     * Get request data.
     *
     * @return array
     */
    public function getRequestData()
    {
        $postData = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);

        return [
            'MerchantID_' => $this->MerchantID,
            'PostData_' => $postData,
        ];
    }
}
