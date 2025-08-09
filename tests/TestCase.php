<?php

namespace Ycs77\NewebPay\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Ycs77\LaravelRecoverSession\RecoverSessionServiceProvider;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Enums\PeriodStartType;
use Ycs77\NewebPay\NewebPayServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2020-01-01 00:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:cVJ8Llv7iMG6ojSDQ1BLqAq+/VktufxMBQiYOerhw4I=');

        $app['config']->set('newebpay.env', 'test');
        /** @deprecated */
        $app['config']->set('newebpay.debug', true);
        $app['config']->set('newebpay.merchant_id', 'TestMerchantID1234');
        $app['config']->set('newebpay.hash_key', 'TestHashKey123456789');
        $app['config']->set('newebpay.hash_iv', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
        $app['config']->set('newebpay.version.mpg', '2.0');
        $app['config']->set('newebpay.version.query', '1.3');
        $app['config']->set('newebpay.version.credit_cancel', '1.0');
        $app['config']->set('newebpay.version.credit_close', '1.1');
        $app['config']->set('newebpay.version.period', '1.5');
        $app['config']->set('newebpay.version.period_status', '1.0');
        $app['config']->set('newebpay.version.period_amt', '1.1');
        $app['config']->set('newebpay.lang', LangType::ZH_TW);
        $app['config']->set('newebpay.return_url', '/pay/callback');
        $app['config']->set('newebpay.notify_url', '/pay/notify');
        $app['config']->set('newebpay.customer_url', '/pay/customer');
        $app['config']->set('newebpay.client_back_url', null);
        $app['config']->set('newebpay.with_session_id', false);
        $app['config']->set('newebpay.payment_methods', [
            'credit' => [
                'enabled' => true,
                'red' => false,
                'inst' => CreditInst::NONE,
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
        $app['config']->set('newebpay.CVSCOM', CVSCOM::NONE);
        $app['config']->set('newebpay.lgs_type', LgsType::DEFAULT);
        $app['config']->set('newebpay.period.start_type', PeriodStartType::AUTHORIZE_NOW);
        $app['config']->set('newebpay.period.payment_info', false);
        $app['config']->set('newebpay.period.order_info', false);
        $app['config']->set('newebpay.period.return_url', null);
        $app['config']->set('newebpay.period.notify_url', null);
        $app['config']->set('newebpay.period.back_url', null);
    }

    protected function getPackageProviders($app)
    {
        return [
            RecoverSessionServiceProvider::class,
            NewebPayServiceProvider::class,
        ];
    }
}
