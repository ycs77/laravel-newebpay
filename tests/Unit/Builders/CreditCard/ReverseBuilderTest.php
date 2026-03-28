<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Ycs77\NewebPay\Builders\CreditCard\ReverseBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\CreditCard\ReverseResult;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $this->response = mock(HttpClientResponse::class);
    $this->response->allows('json')->andReturn(['Status' => 'SUCCESS']);

    $this->factory = app(Factory::class);

    $this->crypto = mock(Crypto::class);
    $this->crypto->allows('setHashKey');
    $this->crypto->allows('setHashIv');
    $this->crypto->allows('encryptByAES')->andReturn('encrypted_data');
    $this->crypto->allows('verifyCheckCode');

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->allows('setTimeout');
    $this->httpTransporter->allows('send')->andReturn($this->response);
});

test('可以使用商店訂單編號取消信用卡交易', function () {
    $expectedOptionsData = [
        'MerchantID' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.0',
            'Amt' => 1050,
            'MerchantOrderNo' => 'Order001',
            'IndexType' => 1,
            'TimeStamp' => Carbon::now()->timestamp,
        ],
    ];

    $result = (new ReverseBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(ReverseResult::class);
});

test('可以使用藍新金流交易序號取消信用卡交易', function () {
    $expectedOptionsData = [
        'MerchantID' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.0',
            'Amt' => 1050,
            'TradeNo' => '23061500000000000',
            'IndexType' => 2,
            'TimeStamp' => Carbon::now()->timestamp,
        ],
    ];

    $result = (new ReverseBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withTrade('23061500000000000')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(ReverseResult::class);
});

test('取消信用卡交易時，商店訂單編號與藍新金流交易序號只能擇一填入', function () {
    (new ReverseBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withTrade('23061500000000000');
})->throws(InvalidArgumentException::class, '商店訂單編號與藍新金流交易序號只能擇一填入');
