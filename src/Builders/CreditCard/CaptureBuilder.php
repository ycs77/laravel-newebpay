<?php

namespace Ycs77\NewebPay\Builders\CreditCard;

use InvalidArgumentException;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Options\CreditCard\CaptureOptions;
use Ycs77\NewebPay\Results\CreditCard\CaptureResult;

final class CaptureBuilder extends Builder
{
    protected CaptureOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));

        $this->options = new CaptureOptions;
        $this->options->merchantId = $this->factory->config('merchant_id');

        $this->endpoint = '/API/CreditCard/Close';
    }

    public function options(): CaptureOptions
    {
        return $this->options;
    }

    /**
     * 商店訂單編號
     *
     * 與藍新金流交易序號二擇一填入
     */
    public function withOrder(string $orderNo): self
    {
        if ($this->options->tradeNo !== '') {
            throw new InvalidArgumentException('商店訂單編號與藍新金流交易序號只能擇一填入');
        }

        $this->options->orderNo = $orderNo;
        $this->options->indexType = 1;

        return $this;
    }

    /**
     * 藍新金流交易序號
     *
     * 與商店訂單編號二擇一填入
     */
    public function withTrade(string $tradeNo): self
    {
        if ($this->options->orderNo !== '') {
            throw new InvalidArgumentException('商店訂單編號與藍新金流交易序號只能擇一填入');
        }

        $this->options->tradeNo = $tradeNo;
        $this->options->indexType = 2;

        return $this;
    }

    /**
     * 請款金額
     */
    public function withAmount(int $amount): self
    {
        $this->options->amount = $amount;

        return $this;
    }

    /**
     * 取消請款
     */
    public function reverse(): self
    {
        $this->options->reverse = true;

        return $this;
    }

    /**
     * 送出信用卡請款請求
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebPayException
     */
    public function send(): CaptureResult
    {
        return new CaptureResult($this->sendRequest());
    }
}
