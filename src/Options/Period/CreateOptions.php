<?php

namespace Ycs77\NewebPay\Options\Period;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\PeriodStartType;
use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Options\Options;

final class CreateOptions extends Options
{
    public string $merchantId = '';

    public string $orderNo = '';

    public int $amount = 0;

    public string $itemDescription = '';

    public string $email = '';

    public ?PeriodType $periodType = null;

    public ?string $periodPoint = null;

    public int $periodTimes = 0;

    public PeriodStartType $periodStartType = PeriodStartType::AUTHORIZE_NOW;

    public ?string $periodFirstdate = null;

    public ?string $returnURL = null;

    public ?string $notifyURL = null;

    public ?string $backURL = null;

    public ?bool $emailModify = null;

    public bool $paymentInfo = true;

    public bool $orderInfo = true;

    public ?LangType $lang = null;

    public bool $unionPay = false;

    public ?string $periodMemo = null;

    public function toArray()
    {
        return [
            'MerchantID_' => $this->merchantId,
            'PostData_' => array_filter([
                'RespondType' => 'JSON',
                'Version' => '1.5',
                'TimeStamp' => Carbon::now()->timestamp,
                'LangType' => $this->lang !== null && $this->lang !== LangType::ZH_TW ? $this->lang->value : null,
                'MerOrderNo' => $this->orderNo,
                'ProdDesc' => $this->itemDescription,
                'PeriodAmt' => $this->amount,
                'PayerEmail' => $this->email,
                'PeriodType' => $this->periodType?->value,
                'PeriodPoint' => $this->periodPoint,
                'PeriodTimes' => $this->periodTimes ?: null,
                'PeriodStartType' => $this->periodStartType->value,
                'PeriodFirstdate' => $this->periodFirstdate,
                'ReturnURL' => $this->returnURL,
                'NotifyURL' => $this->notifyURL,
                'BackURL' => $this->backURL,
                'EmailModify' => $this->emailModify === false ? 0 : null,
                'PaymentInfo' => $this->paymentInfo ? null : 'N',
                'OrderInfo' => $this->orderInfo ? null : 'N',
                'UNIONPAY' => $this->unionPay ? 1 : null,
                'PeriodMemo' => $this->periodMemo,
            ], fn ($value) => ! is_null($value)),
        ];
    }
}
