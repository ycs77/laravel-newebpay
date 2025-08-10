<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ycs77\NewebPay\Builders\Payment\PaymentBuilder payment()
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
