<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;
use Ycs77\NewebPay\Factory;

/**
 * @method static \Ycs77\NewebPay\Resources\Payment payment()
 * @method static \Ycs77\NewebPay\Results\Trade\PaymentResult result(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\Trade\CustomerResult customer(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Resources\PaymentQuery query()
 * @method static \Ycs77\NewebPay\Resources\CreditCard creditCard()
 * @method static \Ycs77\NewebPay\Resources\Period period()
 * @method static \Ycs77\NewebPay\Results\Period\CreateResult periodResult(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\Period\NotifyResult periodNotify(\Illuminate\Http\Request $request)
 *
 * @see Factory
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
        return 'newebpay';
    }
}
