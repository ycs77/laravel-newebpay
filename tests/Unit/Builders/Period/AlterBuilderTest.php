<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Ycs77\NewebPay\Builders\Period\AlterBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\Period\AlterResult;

beforeEach(function () {
    $this->factory = mock(Factory::class);
    $this->factory->allows('baseUrl')->andReturn('https://example.com');

    $this->config = [
        'merchant_id' => 'TestMerchantID1234',
        'hash_key' => 'TestHashKey123456789',
        'hash_iv' => '17ef14e533ed1c18',
        'timeout' => 30,
    ];

    $this->crypto = mock(Crypto::class);
    $this->crypto->allows('setHashKey');
    $this->crypto->allows('setHashIv');
    $this->crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $this->response = mock(HttpClientResponse::class);
    $this->response->allows('json')->andReturn([
        // TODO: 暫時 pass 測試，之後要重構
        'Status' => 'SUCCESS',
        'Period' => [
            'Status' => 'SUCCESS',
        ],
    ]);

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->allows('setTimeout');
    $this->httpTransporter->allows('send')->andReturn($this->response);
});

test('可以成功修改信用卡定期定額委託金額', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterAmt' => 1000,
            'PeriodType' => 'D',
            'PeriodPoint' => '3',
            'PeriodTimes' => 10,
        ],
    ];

    $result = (new AlterBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->everyFewDays(3)
        ->times(10)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterResult::class);
});

test('可以修改委託為每週授權', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterAmt' => 1000,
            'PeriodType' => 'W',
            'PeriodPoint' => '5',
            'PeriodTimes' => 5,
        ],
    ];

    $result = (new AlterBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->weekly(5)
        ->times(5)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterResult::class);
});

test('可以修改委託為每月授權', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterAmt' => 1000,
            'PeriodType' => 'M',
            'PeriodPoint' => '15',
            'PeriodTimes' => 12,
        ],
    ];

    $result = (new AlterBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->monthly(15)
        ->times(12)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterResult::class);
});

test('可以修改委託為每年授權', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterAmt' => 1000,
            'PeriodType' => 'Y',
            'PeriodPoint' => '0615',
            'PeriodTimes' => 3,
        ],
    ];

    $result = (new AlterBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->yearly(6, 15)
        ->times(3)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterResult::class);
});
