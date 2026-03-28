<?php

namespace Ycs77\NewebPay\Builders\Period;

use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\PeriodStartType;
use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Options\Period\CreateOptions;
use Ycs77\NewebPay\Url\UrlFormat;

final class CreateBuilder extends Builder
{
    protected CreateOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));

        $this->options = new CreateOptions;
        $this->options->merchantId = $this->factory->config('merchant_id');

        $this->endpoint = '/MPG/period';

        $this->options->paymentInfo = $this->factory->config('period.payment_info') ?? false;
        $this->options->orderInfo = $this->factory->config('period.order_info') ?? false;

        if ($lang = $this->factory->config('lang')) {
            $this->withLang($lang);
        }

        if ($returnUrl = $this->factory->config('period.return_url')) {
            $this->withReturnUrl($returnUrl);
        }

        if ($notifyUrl = $this->factory->config('period.notify_url')) {
            $this->withNotifyUrl($notifyUrl);
        }

        if ($backUrl = $this->factory->config('period.back_url')) {
            $this->withBackUrl($backUrl);
        }
    }

    public function options(): CreateOptions
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
     * 委託金額
     */
    public function withAmount(int $amount): self
    {
        $this->options->amount = $amount;

        return $this;
    }

    /**
     * 商品資訊
     */
    public function withItemDescription(string $description): self
    {
        $this->options->itemDescription = $description;

        return $this;
    }

    /**
     * 付款人電子信箱
     */
    public function withEmail(string $email): self
    {
        $this->options->email = $email;

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
        $this->options->periodPoint = str_pad((string) $month, 2, '0', STR_PAD_LEFT).str_pad((string) $day, 2, '0', STR_PAD_LEFT);

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
     * 交易模式
     */
    public function startWith(PeriodStartType $startType): self
    {
        $this->options->periodStartType = $startType;

        return $this;
    }

    /**
     * 立即執行十元授權
     */
    public function startWithTenDollarAuth(): self
    {
        return $this->startWith(PeriodStartType::TEN_DOLLARS_NOW);
    }

    /**
     * 立即執行委託金額授權
     */
    public function startWithImmediateAuth(): self
    {
        return $this->startWith(PeriodStartType::AUTHORIZE_NOW);
    }

    /**
     * 不檢查信用卡資訊，不授權
     */
    public function startWithoutAuth(): self
    {
        return $this->startWith(PeriodStartType::NO_AUTHORIZE);
    }

    /**
     * 首期授權日
     */
    public function firstChargeAt(int $year, int $month, int $day): self
    {
        $this->options->periodFirstdate = $year
            .'/'.str_pad((string) $month, 2, '0', STR_PAD_LEFT)
            .'/'.str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        return $this;
    }

    /**
     * 首次付款完成後返回商店網址
     */
    public function withReturnUrl(string $url): self
    {
        $this->options->returnURL = UrlFormat::withSessionIdKey(
            UrlFormat::formatCallbackUrl($url)
        );

        return $this;
    }

    /**
     * 每期授權結果通知網址
     */
    public function withNotifyUrl(string $url): self
    {
        $this->options->notifyURL = UrlFormat::formatCallbackUrl($url);

        return $this;
    }

    /**
     * 語系
     *
     * - 繁體中文 (`LangType::ZH_TW`)
     * - 英文 (`LangType::EN`)
     * - 日文 (`LangType::JP`)
     *
     * 預設值為繁體中文。
     */
    public function withLang(LangType $lang): self
    {
        $this->options->lang = $lang;

        return $this;
    }

    /**
     * 關閉付款人電子信箱修改功能
     */
    public function disableEmailModify(): self
    {
        $this->options->emailModify = false;

        return $this;
    }

    /**
     * 啟用銀聯卡支付
     */
    public function enableUnionPay(): self
    {
        $this->options->unionPay = true;

        return $this;
    }

    /**
     * 委託備註
     */
    public function memo(string $memo): self
    {
        $this->options->periodMemo = $memo;

        return $this;
    }

    /**
     * 取消交易時返回商店的網址
     */
    public function withBackUrl(string $url): self
    {
        $this->options->backURL = UrlFormat::formatCallbackUrl($url);

        return $this;
    }

    /**
     * 送出信用卡定期定額委託表單
     */
    public function submit(): Response
    {
        return $this->sendFormRedirectRequest();
    }
}
