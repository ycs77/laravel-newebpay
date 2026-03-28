<?php

namespace Ycs77\NewebPay\Builders\Trade;

use Carbon\Carbon;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Exceptions\InvalidCheckCodeException;
use Ycs77\NewebPay\Exceptions\NewebPayException;
use Ycs77\NewebPay\Options\Trade\QueryOptions;
use Ycs77\NewebPay\Results\Trade\QueryResult;

class QueryBuilder extends Builder
{
    protected QueryOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);

        $this->options = new QueryOptions;
        $this->options->merchantId = $this->config['merchant_id'];
        $this->options->timestamp = Carbon::now()->timestamp;

        $this->endpoint = '/API/QueryTradeInfo';
    }

    public function options(): QueryOptions
    {
        return $this->options;
    }

    /**
     * 透過訂單編號查詢 (需帶入 訂單編號 + 訂單金額 查詢)
     */
    public function withOrder(string $orderNo)
    {
        $this->options->orderNo = $orderNo;

        return $this;
    }

    /**
     * 透過訂單金額查詢 (需帶入 訂單編號 + 訂單金額 查詢)
     */
    public function withAmount(int $amount)
    {
        $this->options->amount = $amount;

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
    public function withGateway(string $gateway)
    {
        $this->options->gateway = $gateway;

        return $this;
    }

    /**
     * 複合式商店查詢
     *
     * 若為複合式商店(MS5 開頭)，此欄位為必填。
     */
    public function forCompositeStore()
    {
        $this->withGateway('Composite');

        return $this;
    }

    /**
     * 查詢交易結果
     *
     * @throws NewebPayException
     * @throws InvalidCheckCodeException
     */
    public function get(): QueryResult
    {
        $requestData = $this->toRequestData();
        $result = new QueryResult($this->sendRequest($requestData));

        $this->crypto->verifyCheckCode($result);

        return $result;
    }
}
