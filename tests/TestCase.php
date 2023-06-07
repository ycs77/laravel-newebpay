<?php

namespace Ycs77\NewebPay\Tests;

use Illuminate\Contracts\Config\Repository as Config;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Config $config) {
            $config->set('newebpay.Debug', true);
            $config->set('newebpay.MerchantID', 'TestMerchantID1234');
            $config->set('newebpay.HashKey', 'TestHashKey123456789');
            $config->set('newebpay.HashIV', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
            $config->set('newebpay.Version', '1.5');
            $config->set('newebpay.RespondType', 'JSON');
            $config->set('newebpay.LangType', 'zh-tw');
            $config->set('newebpay.TradeLimit', 0);
            $config->set('newebpay.ExpireDate', 7);
            $config->set('newebpay.ReturnURL', null);
            $config->set('newebpay.NotifyURL', null);
            $config->set('newebpay.CustomerURL', null);
            $config->set('newebpay.ClientBackURL', null);
            $config->set('newebpay.EmailModify', false);
            $config->set('newebpay.LoginType', false);
            $config->set('newebpay.OrderComment', null);
            $config->set('newebpay.PaymentMethod', [
                'CREDIT' => [
                    'Enable' => true,
                    'CreditRed' => false,
                    'InstFlag' => 0,
                ],
                'ANDROIDPAY' => false,
                'SAMSUNGPAY' => false,
                'LINEPAY' => false,
                'UNIONPAY' => false,
                'WEBATM' => false,
                'VACC' => false,
                'CVS' => false,
                'BARCODE' => false,
                'ESUNWALLET' => false,
                'TAIWANPAY' => false,
                'EZPAY' => false,
                'EZPWECHAT' => false,
                'EZPALIPAY' => false,
            ]);
            $config->set('newebpay.CVSCOM', null);
            $config->set('newebpay.LgsType', null);
        });
    }
}
