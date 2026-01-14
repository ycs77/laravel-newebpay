<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ycs77\NewebPay\Builders\Payment\PaymentBuilder payment()
 * @method static \Ycs77\NewebPay\Results\Payment\PaymentResult result(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\Payment\CustomerResult customer(\Illuminate\Http\Request $request)
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
        return 'newebpay';
    }
}
