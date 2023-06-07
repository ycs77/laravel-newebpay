<?php

namespace Ycs77\NewebPay;

class NewebPayMPG extends BaseNewebPay
{
    /**
     * The newebpay boot hook.
     */
    public function boot(): void
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
     */
    public function getRequestData(): array
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
