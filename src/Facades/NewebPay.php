<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ycs77\NewebPay\NewebPay
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
        return \Ycs77\NewebPay\NewebPay::class;
    }
}
