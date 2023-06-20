<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ycs77\NewebPay\NewebPayMPG payment(string $no, int $amt, string $desc, string $email)
 * @method static \Ycs77\NewebPay\Results\MPGResult result(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\CustomerResult customer(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\NewebPayQuery query(string $no, int $amt)
 * @method static \Ycs77\NewebPay\NewebPayCancel cancel(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose close(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose request(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose cancelRequest(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose refund(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose cancelRefund(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayPeriod period(string $no, int $amt, string $desc, string $email)
 * @method static \Ycs77\NewebPay\Results\PeriodResult periodResult(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\PeriodNotifyResult periodNotify(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\NewebPayPeriodStatus periodStatus(string $no, string $periodNo, \Ycs77\NewebPay\Enums\PeriodStatus $status)
 * @method static \Ycs77\NewebPay\NewebPayPeriodAmt periodAmt(string $no, string $periodNo, int $amt)
 *
 * @see \Ycs77\NewebPay\Factory
 */
class NewebPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Ycs77\NewebPay\Factory::class;
    }
}
