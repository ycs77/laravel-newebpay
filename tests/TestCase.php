<?php

namespace Ycs77\NewebPay\Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Ycs77\LaravelRecoverSession\RecoverSessionServiceProvider;
use Ycs77\NewebPay\Enums\LangType;
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
        $app['config']->set('newebpay.merchant_id', 'TestMerchantID1234');
        $app['config']->set('newebpay.hash_key', 'TestHashKey123456789');
        $app['config']->set('newebpay.hash_iv', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
        $app['config']->set('newebpay.lang', LangType::ZH_TW);
        $app['config']->set('newebpay.with_session_id', false);
    }

    protected function getPackageProviders($app)
    {
        return [
            RecoverSessionServiceProvider::class,
            NewebPayServiceProvider::class,
        ];
    }
}
