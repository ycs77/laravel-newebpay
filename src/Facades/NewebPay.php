<?php

namespace Webcs4JIG\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Webcs4JIG\NewebPay\NewebPayMPG payment(string $no, int $amt, string $desc, string $email) 付款
 * @method static \Webcs4JIG\NewebPay\NewebPayCancel creditCancel(string $no, int $amt, string $type = 'order')
 * @method static \Webcs4JIG\NewebPay\NewebPayClose requestPayment(string $no, int $amt, string $type = 'order')
 * @method static \Webcs4JIG\NewebPay\NewebPayClose requestRefund(string $no, int $amt, string $type = 'order')
 * @method static \Webcs4JIG\NewebPay\NewebPayQuery query(string $no, int $amt)
 * @method static \Webcs4JIG\NewebPay\NewebPayCreditCard creditcardFirstTrade(array $data)
 * @method static \Webcs4JIG\NewebPay\NewebPayCreditCard creditcardTradeWithToken(array $data)
 * @method static mixed decode(string $encryptString)
 * @method static mixed decodeFromRequest()
 *
 * @see \Webcs4JIG\NewebPay\NewebPay
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
        return \Webcs4JIG\NewebPay\NewebPay::class;
    }
}
