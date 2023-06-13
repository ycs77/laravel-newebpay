<?php

namespace Ycs77\NewebPay;

class NewebPayCreditCard extends BaseNewebPay
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

        $this->setApiPath('API/CreditCard');
        $this->setAsyncSender();

        $this->setVersion();
        $this->setRespondType();
        $this->setP3D();
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
     * 3d 驗證交易
     */
    public function setP3D(bool $p3d = false)
    {
        // 需考慮傳送 notify & return url when p3d is true;
        $this->TradeData['P3D'] = $p3d;

        return $this;
    }

    /**
     * 首次授權信用卡交易
     */
    public function firstTrade(array $data)
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
     */
    public function tradeWithToken(array $data)
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
     */
    public function getRequestData(): array
    {
        $tradeInfo = $this->encryptDataByAES($this->TradeData, $this->hashKey, $this->hashIV);

        return [
            'MerchantID_' => $this->merchantID,
            'PostData_' => $tradeInfo,
            'Pos_' => $this->config->get('newebpay.respond_type'),
        ];
    }
}
