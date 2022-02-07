<?php

namespace Ycs77\NewebPay;

class NewebPayClose extends BaseNewebPay
{
    /**
     * The newebpay boot hook.
     *
     * @return void
     */
    public function boot()
    {
        $this->setApiPath('API/CreditCard/Close');
        $this->setAsyncSender();

        $this->setNotifyURL();
    }

    /**
     * 設定請退款的模式
     *
     * @param  string  $no
     * @param  int  $amt
     * @param  string  $type
     *                        'order': 使用商店訂單編號
     *                        'trade': 使用藍新金流交易序號
     * @return $this
     */
    public function setCloseOrder($no, $amt, $type = 'order')
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
     * @param  string  $type
     *                        'pay': 請款
     *                        'refund': 退款
     * @return $this
     */
    public function setCloseType($type = 'pay')
    {
        if ($type === 'pay') {
            $this->TradeData['CloseType'] = 1;
        } elseif ($type === 'refund') {
            $this->TradeData['CloseType'] = 2;
        }

        return $this;
    }

    public function setCancel($isCancel = false)
    {
        $this->TradeData['Cancel'] = $isCancel;

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
