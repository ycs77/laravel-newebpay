<?php

namespace Ycs77\NewebPay\Tests;

use Ycs77\NewebPay\NewebPay;

class EncryptTradeData extends NewebPay
{
    /**
     * The newebpay trade data.
     */
    protected array $tradeData = [];

    /**
     * Get the newebpay trade data.
     */
    public function tradeData(): array
    {
        return $this->tradeData;
    }

    /**
     * Set the newebpay trade data.
     */
    public function setTradeData(array $tradeData)
    {
        $this->tradeData = $tradeData;

        return $this;
    }

    /**
     * Encrypt data request data.
     */
    public function encryptData(): array
    {
        $tradeInfo = $this->encryptDataByAES($this->tradeData, $this->hashKey, $this->hashIV, $type = 'JSON');
        $tradeSha = $this->encryptDataBySHA($tradeInfo, $this->hashKey, $this->hashIV);

        return [
            'TradeInfo' => $tradeInfo,
            'TradeSha' => $tradeSha,
        ];
    }
}
