<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Ycs77\NewebPay\Builders\Trade\QueryBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\Trade\QueryResult;

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
    $this->crypto->allows('encodeCheckValue')->andReturn('encrypted_data');
    $this->crypto->allows('verifyCheckCode');

    $this->response = mock(HttpClientResponse::class);
    $this->response->allows('json')->andReturn(['Status' => 'SUCCESS']);

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->allows('setTimeout');
    $this->httpTransporter->allows('send')->andReturn($this->response);
});

test('可以成功查詢交易結果', function () {
    $expectedOptionsData = [
        'MerchantID' => 'TestMerchantID1234',
        'Version' => '1.3',
        'RespondType' => 'JSON',
        'CheckValue' => [
            'MerchantID' => 'TestMerchantID1234',
            'MerchantOrderNo' => 'Order001',
            'Amt' => 1050,
        ],
        'TimeStamp' => Carbon::now()->timestamp,
        'MerchantOrderNo' => 'Order001',
        'Amt' => 1050,
    ];

    $result = (new QueryBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->get();

    expect($result)->toBeInstanceOf(QueryResult::class);
});
