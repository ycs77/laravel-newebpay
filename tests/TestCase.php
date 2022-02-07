<?php

namespace Ycs77\NewebPay\Test;

use Carbon\Carbon;
use Illuminate\Contracts\Config\Repository as Config;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setTestNow()
    {
        Carbon::setTestNow(Carbon::create(2020, 1, 1));
    }

    /**
     * Create mock config.
     *
     * @return \Illuminate\Contracts\Config\Repository|\Mockery\MockInterface
     */
    public function createMockConfig()
    {
        /** @var \Illuminate\Contracts\Config\Repository|\Mockery\MockInterface */
        $config = m::mock(Config::class);

        $this->mockConfigValues($config);

        return $config;
    }

    /**
     * Mock the config.
     *
     * @param  \Mockery\MockInterface  $mock
     * @param  string  $key
     * @param  mixed  $returnValue
     * @return void
     */
    public function mockGetConfig($mock, $key, $returnValue)
    {
        $withArgs = array_filter(["newebpay.$key"], function ($value) {
            return $value !== null;
        });

        $mock->shouldReceive('get')
            ->withArgs($withArgs)
            ->andReturn($returnValue);
    }

    /**
     * Mock the config.
     *
     * @param  \Mockery\MockInterface  $config
     * @return void
     */
    public function mockConfigValues(MockInterface $config)
    {
        $this->mockGetConfig($config, 'Debug', true);
        $this->mockGetConfig($config, 'MerchantID', 'TestMerchantID1234');
        $this->mockGetConfig($config, 'HashKey', 'TestHashKey123456789');
        $this->mockGetConfig($config, 'HashIV', '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
        $this->mockGetConfig($config, 'Version', '1.5');
        $this->mockGetConfig($config, 'RespondType', 'JSON');
        $this->mockGetConfig($config, 'LangType', 'zh-tw');
        $this->mockGetConfig($config, 'TradeLimit', 0);
        $this->mockGetConfig($config, 'ExpireDate', 7);
        $this->mockGetConfig($config, 'ReturnURL', null);
        $this->mockGetConfig($config, 'NotifyURL', null);
        $this->mockGetConfig($config, 'CustomerURL', null);
        $this->mockGetConfig($config, 'ClientBackURL', null);
        $this->mockGetConfig($config, 'EmailModify', false);
        $this->mockGetConfig($config, 'LoginType', false);
        $this->mockGetConfig($config, 'OrderComment', null);
        $this->mockGetConfig($config, 'PaymentMethod', [
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
        $this->mockGetConfig($config, 'CVSCOM', null);
        $this->mockGetConfig($config, 'LgsType', null);
    }
}
