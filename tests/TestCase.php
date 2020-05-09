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
        $config = m::mock(Config::class);

        $this->mockConfigValues($config);

        return $config;
    }

    public function mockGetConfig(MockInterface $mock, $key, $default = null, $returnValue)
    {
        $withArgs = array_filter(array("newebpay.$key", $default), function ($value) {
            return $value !== null;
        });

        $mock->shouldReceive('get')
            ->withArgs($withArgs)
            ->andReturn($returnValue);
    }

    public function mockConfigValues(MockInterface $config)
    {
        $this->mockGetConfig($config, 'Debug', null, true);
        $this->mockGetConfig($config, 'MerchantID', null, 'TestMerchantID1234');
        $this->mockGetConfig($config, 'HashKey', null, 'TestHashKey123456789');
        $this->mockGetConfig($config, 'HashIV', null, '17ef14e533ed1c18'); // Generate with `bin2hex(openssl_random_pseudo_bytes(8));`
        $this->mockGetConfig($config, 'Version', '1.5', '1.5');
        $this->mockGetConfig($config, 'RespondType', 'json', 'json');
        $this->mockGetConfig($config, 'LangType', 'zh-tw', 'zh-tw');
        $this->mockGetConfig($config, 'TradeLimit', 0, 0);
        $this->mockGetConfig($config, 'ExpireDate', 7, 7);
        $this->mockGetConfig($config, 'ReturnURL', null, null);
        $this->mockGetConfig($config, 'NotifyURL', null, null);
        $this->mockGetConfig($config, 'CustomerURL', null, null);
        $this->mockGetConfig($config, 'ClientBackURL', null, null);
        $this->mockGetConfig($config, 'EmailModify', false, false);
        $this->mockGetConfig($config, 'LoginType', false, false);
        $this->mockGetConfig($config, 'OrderComment', null, null);
        $this->mockGetConfig($config, 'PaymentMethod', null, [
            'CREDIT' => [
                'Enable' => true,
                'CreditRed' => false,
                'InstFlag' => 0,
            ],
            'ANDROIDPAY' => false,
            'SAMSUNGPAY' => false,
            'UNIONPAY' => false,
            'WEBATM' => false,
            'VACC' => false,
            'CVS' => false,
            'BARCODE' => false,
            'P2G' => false,
        ]);
        $this->mockGetConfig($config, 'CVSCOM', null, null);
    }
}
