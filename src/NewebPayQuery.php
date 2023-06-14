<?php

namespace Ycs77\NewebPay;

use Ycs77\NewebPay\Enums\RespondType;

class NewebPayQuery extends BaseNewebPay
{
    /**
     * The newebpay check values data.
     */
    protected array $checkValues = [];

    /**
     * The newebpay gateway data.
     */
    protected ?string $gateway = null;

    /**
     * The newebpay boot hook.
     */
    public function boot(): void
    {
        $this->setBackgroundSender();

        $this->checkValues['MerchantID'] = $this->merchantID;

        $this->apiPath('/API/QueryTradeInfo');
        $this->respondType();
    }

    /**
     * 回傳格式
     *
     * 回傳格式可設定 JSON 或 String。
     */
    public function respondType(RespondType $type = null)
    {
        $this->respondType = $type
            ? $type->value
            : $this->config->get('newebpay.respond_type')->value;

        return $this;
    }

    /**
     * 單筆交易查詢
     *
     * @param  string  $no  訂單編號
     * @param  int  $amt  訂單金額
     */
    public function query(string $no, int $amt)
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
    public function gateway(string $gateway = null)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Get request data.
     */
    public function requestData(): array
    {
        $CheckValue = $this->queryCheckValue($this->checkValues, $this->hashKey, $this->hashIV);

        return [
            'MerchantID' => $this->merchantID,
            'Version' => $this->config->get('newebpay.query_version'),
            'RespondType' => $this->respondType,
            'CheckValue' => $CheckValue,
            'TimeStamp' => $this->timestamp,
            'MerchantOrderNo' => $this->checkValues['MerchantOrderNo'],
            'Amt' => $this->checkValues['Amt'],
            'Gateway' => $this->gateway,
        ];
    }
}
