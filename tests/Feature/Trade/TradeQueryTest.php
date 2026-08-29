<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Options\Trade\QueryOptions;
use Ycs77\NewebPay\Resources\PaymentQuery;
use Ycs77\NewebPay\Results\Trade\QueryResult;

use function Pest\Laravel\partialMock;

test('交易查詢 → 成功查詢', function () {
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
    $crypto->allows('encodeCheckValue')->andReturn('encrypted_data');
    $crypto->allows('verifyCheckCode');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => '1',
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => '123456789',
                'FundTime' => '2023-01-01',
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
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});

test('交易查詢 → 進階查詢', function () {
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
    $crypto->allows('encodeCheckValue')->andReturn('encrypted_data');
    $crypto->allows('verifyCheckCode');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => '1',
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => '123456789',
                'FundTime' => '2023-01-01',
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

test('交易查詢 → 模擬查詢交易結果', function () {
    NewebPay::fake([
        QueryResult::make([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => '1',
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => '123456789',
                'FundTime' => '2023-01-01',
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
        ]),
    ]);

    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->get();

    NewebPay::assertSent(PaymentQuery::class, 'query', function (QueryOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->amount === 1050;
    });

    expect($result)->toBeInstanceOf(QueryResult::class)
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});
