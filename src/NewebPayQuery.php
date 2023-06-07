<?php

namespace Ycs77\NewebPay;

class NewebPayQuery extends BaseNewebPay
{
    protected array $checkValues;

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setApiPath('API/QueryTradeInfo');
        $this->setAsyncSender();

        $this->checkValues['MerchantID'] = $this->MerchantID;
    }

    /**
     * 查詢訂單
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     */
    public function setQuery(string $no, int $amt): self
    {
        $this->checkValues['MerchantOrderNo'] = $no;
        $this->checkValues['Amt'] = $amt;

        return $this;
    }

    /**
     * Get request data.
     */
    public function getRequestData(): array
    {
        $CheckValue = $this->queryCheckValue($this->checkValues, $this->HashKey, $this->HashIV);

        return [
            'MerchantID' => $this->MerchantID,
            'Version' => $this->config->get('newebpay.version'),
            'RespondType' => $this->config->get('newebpay.respond_type'),
            'CheckValue' => $CheckValue,
            'TimeStamp' => $this->timestamp,
            'MerchantOrderNo' => $this->checkValues['MerchantOrderNo'],
            'Amt' => $this->checkValues['Amt'],
        ];
    }
}
