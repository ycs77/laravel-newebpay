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
            $config->set('newebpay.debug', true);
            $config->set('newebpay.merchant_id', 'TestMerchantID1234');
            $config->set('newebpay.hash_key', 'TestHashKey123456789');
            $config->set('newebpay.hash_iv', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
            $config->set('newebpay.version', '1.5');
            $config->set('newebpay.respond_type', 'JSON');
            $config->set('newebpay.lang_type', 'zh-tw');
            $config->set('newebpay.trade_limit', 0);
            $config->set('newebpay.expire_date', 7);
            $config->set('newebpay.return_url', null);
            $config->set('newebpay.notify_url', null);
            $config->set('newebpay.customer_url', null);
            $config->set('newebpay.client_back_url', null);
            $config->set('newebpay.email_modify', false);
            $config->set('newebpay.login_type', false);
            $config->set('newebpay.order_comment', null);
            $config->set('newebpay.payment_method', [
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
            $config->set('newebpay.lgs_type', null);
        });
    }
}
