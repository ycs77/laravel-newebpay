<?php

namespace Ycs77\NewebPay;

class NewebPayCreditCard extends BaseNewebPay
{
    /**
     * The newebpay boot hook.
     *
     * @return void
     */
    public function boot()
    {
        $this->setApiPath('API/CreditCard');
        $this->setAsyncSender();

        $this->setP3D(false);
    }

    /**
     * 3d 驗證交易
     *
     * @param  bool  $p3d
     * @return self
     */
    public function setP3D($p3d = false)
    {
        // 需考慮傳送 notify & return url when p3d is true;
        $this->TradeData['P3D'] = $p3d;

        return $this;
    }

    /**
     * 首次授權信用卡交易
     *
     * @param  array  $data
     * @return self
     */
    public function firstTrade($data)
    {
        $this->TradeData['TokenSwitch'] = 'get';

        $this->TradeData['MerchantOrderNo'] = $data['no'];
        $this->TradeData['Amt'] = $data['amt'];
        $this->TradeData['ProdDesc'] = $data['desc'];
        $this->TradeData['PayerEmail'] = $data['email'];
        $this->TradeData['CardNo'] = $data['cardNo'];
        $this->TradeData['Exp'] = $data['exp'];
        $this->TradeData['CVC'] = $data['cvc'];
        $this->TradeData['TokenTerm'] = $data['tokenTerm'];

        return $this;
    }

    /**
     * 使用 Token 授權
     *
     * @param  array  $data
     * @return self
     */
    public function tradeWithToken($data)
    {
        $this->TradeData['TokenSwitch'] = 'on';

        $this->TradeData['MerchantOrderNo'] = $data['no'];
        $this->TradeData['Amt'] = $data['amt'];
        $this->TradeData['ProdDesc'] = $data['desc'];
        $this->TradeData['PayerEmail'] = $data['email'];
        $this->TradeData['TokenValue'] = $data['tokenValue'];
        $this->TradeData['TokenTerm'] = $data['tokenTerm'];

        return $this;
    }

    /**
     * Get request data.
     *
     * @return array
     */
    public function getRequestData()
    {
        $tradeInfo = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);

        return [
            'MerchantID_' => $this->MerchantID,
            'PostData_' => $tradeInfo,
            'Pos_' => $this->config->get('newebpay.RespondType'),
        ];
    }
}
