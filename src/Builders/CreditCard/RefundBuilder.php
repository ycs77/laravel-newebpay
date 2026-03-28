<?php

namespace Ycs77\NewebPay\Builders\CreditCard;

use InvalidArgumentException;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Options\CreditCard\RefundOptions;
use Ycs77\NewebPay\Results\CreditCard\RefundResult;

final class RefundBuilder extends Builder
{
    protected RefundOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);

        $this->options = new RefundOptions;
        $this->options->merchantId = $this->config['merchant_id'];

        $this->endpoint = '/API/CreditCard/Close';
    }

    public function options(): RefundOptions
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
     * 退款金額
     */
    public function withAmount(int $amount): self
    {
        $this->options->amount = $amount;

        return $this;
    }

    /**
     * 取消退款
     */
    public function reverse(): self
    {
        $this->options->reverse = true;

        return $this;
    }

    /**
     * 送出退款信用卡交易請求
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebPayException
     */
    public function send(): RefundResult
    {
        return new RefundResult($this->sendRequest());
    }
}
