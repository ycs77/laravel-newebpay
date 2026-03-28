<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\CreditCard\RefundResult;

use function Pest\Laravel\partialMock;

test('可以成功呼叫信用卡退款功能', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

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
            'CloseType' => 2,
        ],
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '退款成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
            ],
        ], 200),
    ]);

    $result = NewebPay::creditCard()
        ->refund()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(RefundResult::class)
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});

test('可以成功呼叫取消退款功能', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

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
            'CloseType' => 2,
            'Cancel' => 1,
        ],
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '取消退款成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
            ],
        ], 200),
    ]);

    $result = NewebPay::creditCard()
        ->refund()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->reverse()
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(RefundResult::class)
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});
