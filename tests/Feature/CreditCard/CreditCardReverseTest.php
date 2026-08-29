<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\CreditCard\ReverseOptions;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Resources\CreditCard;
use Ycs77\NewebPay\Results\CreditCard\ReverseResult;

use function Pest\Laravel\partialMock;

test('信用卡取消交易 → 成功取消', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.0',
            'Amt' => 1050,
            'MerchantOrderNo' => 'Order001',
            'IndexType' => 1,
            'TimeStamp' => Carbon::now()->timestamp,
        ],
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');
    $crypto->allows('verifyCheckCode');

    Http::fake([
        '*' => Http::response([
            'Status' => 'SUCCESS',
            'Message' => '取消授權成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'CheckCode' => '123456789',
            ],
        ], 200),
    ]);

    $result = NewebPay::creditCard()
        ->reverse()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(ReverseResult::class)
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});

test('信用卡取消交易 → 模擬取消交易', function () {
    NewebPay::fake([
        ReverseResult::make([
            'Status' => 'SUCCESS',
            'Message' => '取消授權成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'CheckCode' => '123456789',
            ],
        ]),
    ]);

    $result = NewebPay::creditCard()
        ->reverse()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->send();

    NewebPay::assertSent(CreditCard::class, 'reverse', function (ReverseOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->amount === 1050;
    });

    expect($result)->toBeInstanceOf(ReverseResult::class)
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});
