<?php

namespace Ycs77\NewebPay\Builders\Period;

use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Options\Period\AlterOptions;
use Ycs77\NewebPay\Results\Period\AlterResult;

final class AlterBuilder extends Builder
{
    protected AlterOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));

        $this->options = new AlterOptions;
        $this->options->merchantId = $this->factory->config('merchant_id');

        $this->endpoint = '/MPG/period/AlterAmt';
    }

    public function options(): AlterOptions
    {
        return $this->options;
    }

    /**
     * 商店訂單編號
     */
    public function withOrder(string $orderNo): self
    {
        $this->options->orderNo = $orderNo;

        return $this;
    }

    /**
     * 委託單號
     */
    public function withPeriod(string $periodNo): self
    {
        $this->options->periodNo = $periodNo;

        return $this;
    }

    /**
     * 委託金額
     */
    public function withAmount(int $amount): self
    {
        $this->options->alterAmt = $amount;

        return $this;
    }

    /**
     * 設定此委託於固定天期制觸發
     *
     * @param  int  $day  執行委託的間隔天數（2~999）
     */
    public function everyFewDays(int $day): self
    {
        $this->options->periodType = PeriodType::EVERY_FEW_DAYS;
        $this->options->periodPoint = (string) $day;

        return $this;
    }

    /**
     * 設定此委託於每週觸發
     *
     * @param  int  $weekday  在週幾執行委託（1~7）
     */
    public function weekly(int $weekday): self
    {
        $this->options->periodType = PeriodType::WEEKLY;
        $this->options->periodPoint = (string) $weekday;

        return $this;
    }

    /**
     * 設定此委託於每月觸發
     *
     * @param  int  $day  在每月的第幾天執行委託（1~31）
     */
    public function monthly(int $day): self
    {
        $this->options->periodType = PeriodType::MONTHLY;
        $this->options->periodPoint = str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return $this;
    }

    /**
     * 設定此委託於每年觸發
     */
    public function yearly(int $month, int $day): self
    {
        $this->options->periodType = PeriodType::YEARLY;
        $this->options->periodPoint = str_pad((string) $month, 2, '0', STR_PAD_LEFT)
            .str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return $this;
    }

    /**
     * 授權期數
     *
     * @param  int  $times  授權委託的期數（1~99）
     */
    public function times(int $times): self
    {
        $this->options->periodTimes = $times;

        return $this;
    }

    /**
     * 調整信用卡到期日
     */
    public function creditExpiredAt(int $month, int $day): self
    {
        $this->options->extday = str_pad((string) $month, 2, '0', STR_PAD_LEFT)
            .str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return $this;
    }

    /**
     * 送出修改委託金額請求
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebPayException
     */
    public function send(): AlterResult
    {
        return new AlterResult($this->sendRequest());
    }
}
