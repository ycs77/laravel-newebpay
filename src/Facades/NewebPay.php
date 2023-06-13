<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ycs77\NewebPay\NewebPayMPG payment(string $no, int $amt, string $desc, string $email)
 * @method static \Ycs77\NewebPay\NewebPayQuery query(string $no, int $amt)
 * @method static \Ycs77\NewebPay\NewebPayCancel creditCancel(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose requestPayment(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose requestRefund(string $no, int $amt, string $type = 'order')
 * @method static mixed decode(string $encryptString)
 * @method static mixed decodeFromRequest()
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
