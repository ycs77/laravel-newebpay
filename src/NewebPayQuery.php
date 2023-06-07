<?php

namespace Ycs77\NewebPay;

class NewebPayQuery extends BaseNewebPay
{
    protected $CheckValues;

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setApiPath('API/QueryTradeInfo');
        $this->setAsyncSender();

        $this->CheckValues['MerchantID'] = $this->MerchantID;
    }

    /**
     * 查詢訂單
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     */
    public function setQuery(string $no, int $amt): self
    {
        $this->CheckValues['MerchantOrderNo'] = $no;
        $this->CheckValues['Amt'] = $amt;

        return $this;
    }

    /**
     * Get request data.
     */
    public function getRequestData(): array
    {
        $CheckValue = $this->queryCheckValue($this->CheckValues, $this->HashKey, $this->HashIV);

        return [
            'MerchantID' => $this->MerchantID,
            'Version' => $this->config->get('newebpay.version'),
            'RespondType' => $this->config->get('newebpay.respond_type'),
            'CheckValue' => $CheckValue,
            'TimeStamp' => $this->timestamp,
            'MerchantOrderNo' => $this->CheckValues['MerchantOrderNo'],
            'Amt' => $this->CheckValues['Amt'],
        ];
    }
}
