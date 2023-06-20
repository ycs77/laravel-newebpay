<?php

namespace Ycs77\NewebPay\Tests;

use Illuminate\Contracts\Config\Repository as Config;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Enums\PeriodStartType;

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
            $config->set('app.key', 'base64:cVJ8Llv7iMG6ojSDQ1BLqAq+/VktufxMBQiYOerhw4I=');

            $config->set('newebpay.debug', true);
            $config->set('newebpay.merchant_id', 'TestMerchantID1234');
            $config->set('newebpay.hash_key', 'TestHashKey123456789');
            $config->set('newebpay.hash_iv', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
            $config->set('newebpay.version.mpg', '2.0');
            $config->set('newebpay.version.query', '1.3');
            $config->set('newebpay.version.credit_cancel', '1.0');
            $config->set('newebpay.version.credit_close', '1.1');
            $config->set('newebpay.version.period', '1.5');
            $config->set('newebpay.version.period_status', '1.0');
            $config->set('newebpay.version.period_amt', '1.1');
            $config->set('newebpay.lang', 'zh-tw');
            $config->set('newebpay.trade_limit', 0);
            $config->set('newebpay.expire_date', 7);
            $config->set('newebpay.return_url', '/pay/callback');
            $config->set('newebpay.notify_url', '/pay/notify');
            $config->set('newebpay.customer_url', '/pay/customer');
            $config->set('newebpay.client_back_url', null);
            $config->set('newebpay.with_session_id', false);
            $config->set('newebpay.email_modify', false);
            $config->set('newebpay.login_type', false);
            $config->set('newebpay.order_comment', null);
            $config->set('newebpay.payment_methods', [
                'credit' => [
                    'enabled' => true,
                    'red' => false,
                    'inst' => CreditInst::NONE,
                ],
                'credit_remember' => [
                    'enabled' => false,
                    'demand' => CreditRememberDemand::EXPIRATION_DATE_AND_CVC,
                ],
                'webATM' => false,
                'VACC' => false,
                'bank' => Bank::ALL,
                'NTCB' => [
                    'enabled' => false,
                    'locate' => NTCBLocate::TaipeiCity,
                    'start_date' => '2015-01-01',
                    'end_date' => '2015-01-01',
                ],
                'googlePay' => false,
                'samsungPay' => false,
                'linePay' => [
                    'enabled' => false,
                ],
                'unionPay' => false,
                'esunWallet' => false,
                'taiwanPay' => false,
                'ezPay' => false,
                'ezpWeChat' => false,
                'ezpAlipay' => false,
                'CVS' => false,
                'barcode' => false,
            ]);
            $config->set('newebpay.CVSCOM', CVSCOM::NONE);
            $config->set('newebpay.lgs_type', LgsType::DEFAULT);
            $config->set('newebpay.period.start_type', PeriodStartType::AUTHORIZE_NOW);
            $config->set('newebpay.period.payment_info', false);
            $config->set('newebpay.period.order_info', false);
            $config->set('newebpay.period.return_url', null);
            $config->set('newebpay.period.notify_url', null);
            $config->set('newebpay.period.back_url', null);
        });
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            'Ycs77\LaravelRecoverSession\RecoverSessionServiceProvider',
            'Ycs77\NewebPay\NewebPayServiceProvider',
        ];
    }
}
