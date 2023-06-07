<?php

namespace Ycs77\NewebPay;

class NewebPayQuery extends BaseNewebPay
{
    protected array $checkValues;
    protected ?string $gateway = null;

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
     * 單筆交易查詢
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
     * 資料來源
     *
     * 設定此參數會查詢 複合式商店旗下對應商店的訂單。
     *
     * 若為複合式商店(MS5 開頭)，此欄位為必填，且要固定填入："Composite"。
     * 若沒有帶[Gateway]或是帶入其他參數值，則查詢一般商店代號。
     */
    public function setGateway(string $gateway = null): self
    {
        $this->gateway = $gateway;

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
            'Gateway' => $this->gateway,
        ];
    }
}
