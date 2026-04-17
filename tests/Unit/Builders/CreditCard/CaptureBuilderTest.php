<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Ycs77\NewebPay\Builders\CreditCard\CaptureBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\CreditCard\CaptureResult;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $this->factory = mock(Factory::class);
    $this->factory->allows('baseUrl')->andReturn('https://example.com');
    $this->factory->allows('record')->andReturnNull();

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
    $this->response->allows('json')->andReturn(['Status' => 'SUCCESS']);

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->allows('setTimeout');
    $this->httpTransporter->allows('send')->andReturn($this->response);
});

test('可以使用商店訂單編號請款', function () {
    $expectedPostData = [
        'RespondType' => 'JSON',
        'Version' => '1.1',
        'Amt' => 1050,
        'MerchantOrderNo' => 'Order001',
        'TimeStamp' => Carbon::now()->timestamp,
        'IndexType' => 1,
        'TradeNo' => '',
        'CloseType' => 1,
    ];

    $result = (new CaptureBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedPostData) {
            expect($options->toArray()['PostData_'])->toBe($expectedPostData);
        })
        ->send();

    expect($result)->toBeInstanceOf(CaptureResult::class);
});

test('可以使用藍新金流交易序號請款', function () {
    $expectedOptionsData = [
        'MerchantID' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'Amt' => 1050,
            'MerchantOrderNo' => '',
            'TimeStamp' => Carbon::now()->timestamp,
            'IndexType' => 2,
            'TradeNo' => '23061500000000000',
            'CloseType' => 1,
        ],
    ];

    $result = (new CaptureBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withTrade('23061500000000000')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(CaptureResult::class);
});

test('可以取消請款', function () {
    $expectedOptionsData = [
        'MerchantID' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.1',
            'Amt' => 1050,
            'MerchantOrderNo' => 'Order001',
            'TimeStamp' => Carbon::now()->timestamp,
            'IndexType' => 1,
            'TradeNo' => '',
            'CloseType' => 1,
            'Cancel' => 1,
        ],
    ];

    $result = (new CaptureBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withAmount(1050)
        ->reverse()
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(CaptureResult::class);
});

test('請款時商店訂單編號與藍新金流交易序號只能擇一填入', function () {
    (new CaptureBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withTrade('23061500000000000');
})->throws(InvalidArgumentException::class, '商店訂單編號與藍新金流交易序號只能擇一填入');
