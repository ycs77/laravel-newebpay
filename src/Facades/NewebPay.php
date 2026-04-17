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
 * @method static void fake(\Ycs77\NewebPay\Results\Result[] $results)
 * @method static bool recording()
 * @method static \Ycs77\NewebPay\Results\Result|null record(string $resource, ?string $action, \Ycs77\NewebPay\Options\Options $options)
 * @method static void assertSent(string $resource, string|callable|null $action, ?callable $callback = null)
 * @method static void assertNotSent(string $resource, string|callable|null $action, ?callable $callback = null)
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
