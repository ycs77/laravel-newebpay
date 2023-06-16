<?php

namespace Ycs77\NewebPay\Facades;

use Illuminate\Support\Facades\Facade;
use Ycs77\NewebPay\Testing\EncryptTradeDataTesting;

/**
 * @method static \Ycs77\NewebPay\NewebPayMPG payment(string $no, int $amt, string $desc, string $email)
 * @method static \Ycs77\NewebPay\Results\MPGResult result(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\Results\CustomerResult customer(\Illuminate\Http\Request $request)
 * @method static \Ycs77\NewebPay\NewebPayQuery query(string $no, int $amt)
 * @method static \Ycs77\NewebPay\NewebPayCancel cancel(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose request(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose cancelRequest(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose refund(string $no, int $amt, string $type = 'order')
 * @method static \Ycs77\NewebPay\NewebPayClose cancelRefund(string $no, int $amt, string $type = 'order')
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

    /**
     * Encryption the fake trade data for testing.
     */
    public static function encryptTradeDataForTesting(array $tradeData): array
    {
        $response = new EncryptTradeDataTesting(
            static::$app->make('config'),
            static::$app->make('session.store')
        );

        $response->setTradeData($tradeData);

        return $response->encryptData();
    }
}
