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

    $this->response = mock(HttpClientResponse::class);
    $this->response->expects('json')->andReturn(['Status' => 'SUCCESS']);

    $this->factory = app(Factory::class);

    $this->crypto = mock(Crypto::class);
    $this->crypto->expects('setHashKey');
    $this->crypto->expects('setHashIv');
    $this->crypto->expects('encodeCheckValue')->andReturn('encrypted_data');
    $this->crypto->expects('verifyCheckCode');

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->expects('send')->andReturn($this->response);
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

    $result = (new QueryBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->get();

    expect($result)->toBeInstanceOf(QueryResult::class);
});
