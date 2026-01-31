<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\Trade\QueryResult;

use function Pest\Laravel\partialMock;

test('可以成功呼叫查詢交易功能', function () {
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

    $crypto = partialMock(Crypto::class);
    $crypto->expects('encodeCheckValue')->andReturn('encrypted_data');
    $crypto->expects('verifyCheckCode');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => 1,
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
                'FundTime' => '2023-01-01 00:00:00',
                'RespondCode' => '00',
                'Auth' => '222111',
                'ECI' => '',
                'CloseAmt' => 120,
                'CloseStatus' => 0,
                'BackBalance' => 120,
                'BackStatus' => 0,
                'RespondMsg' => '授權測試',
                'Inst' => 0,
                'InstFirst' => 0,
                'InstEach' => 0,
                'PaymentMethod' => 'CREDIT',
                'Card6No' => '400022',
                'Card4No' => '1111',
                'AuthBank' => 'CTBC',
            ],
        ], 200),
    ]);

    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->get();

    expect($result)->toBeInstanceOf(QueryResult::class)
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});

test('可以成功呼叫查詢交易的更多功能', function () {
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
        'Gateway' => 'Composite',
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->expects('encodeCheckValue')->andReturn('encrypted_data');
    $crypto->expects('verifyCheckCode');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => 1,
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
                'FundTime' => '2023-01-01 00:00:00',
                'RespondCode' => '00',
                'Auth' => '222111',
                'ECI' => '',
                'CloseAmt' => 120,
                'CloseStatus' => 0,
                'BackBalance' => 120,
                'BackStatus' => 0,
                'RespondMsg' => '授權測試',
                'Inst' => 0,
                'InstFirst' => 0,
                'InstEach' => 0,
                'PaymentMethod' => 'CREDIT',
                'Card6No' => '400022',
                'Card4No' => '1111',
                'AuthBank' => 'CTBC',
            ],
        ], 200),
    ]);

    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->forCompositeStore()
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->get();

    expect($result)->toBeInstanceOf(QueryResult::class);
});
