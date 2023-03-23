<?php

namespace Webcs4JIG\NewebPay;

class NewebPayTradeInfo extends BaseNewebPay
{
    /**
     * The newebpay boot hook.
     *
     * @return void
     */
    public function boot()
    {
        $this->setApiPath('MPG/mpg_gateway');
        $this->setSyncSender();

        $this->setLangType();
        $this->setTradeLimit();
        $this->setExpireDate();
        $this->setReturnURL();
        $this->setNotifyURL();
        $this->setCustomerURL();
        $this->setClientBackURL();
        $this->setEmailModify();
        $this->setLoginType();
        $this->setOrderComment();
        $this->setPaymentMethod();
        $this->setTokenTerm();
        $this->setCVSCOM();
        $this->setLgsType();

        $this->TradeData['MerchantID'] = $this->MerchantID;
    }

    /**
     * Get request data.
     *
     * @return array
     */
    public function getRequestData()
    {
        $tradeInfo = $this->encryptDataByAES($this->TradeData, $this->HashKey, $this->HashIV);
        $tradeSha = $this->encryptDataBySHA($tradeInfo, $this->HashKey, $this->HashIV);

        return [
            'MerchantID' => $this->MerchantID,
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
            'Version' => $this->TradeData['Version'],
        ];
    }
}
