<?php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Results\Period\CreateResult;
use Ycs77\NewebPay\Results\Period\NotifyResult;

use function Pest\Laravel\partialMock;

test('可以解析信用卡定期定額委託回傳資料', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->allows('decryptByAES')->andReturn([
        'Status' => 'SUCCESS',
        'Message' => '委託建立成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'MerchantOrderNo' => 'Order001',
            'PeriodType' => 'M',
            'AuthTimes' => 6,
            'DateArray' => '2020-02-01,2020-03-01,2020-04-01,2020-05-01,2020-06-01,2020-07-01',
            'PeriodAmt' => 1050,
            'PeriodNo' => '20200101000000001',
        ],
    ]);

    $result = NewebPay::periodResult(Request::create('/callback', 'POST', [
        'Period' => 'encrypted_period_data',
    ]));

    expect($result)->toBeInstanceOf(CreateResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodNo())->toBe('20200101000000001')
        ->and($result->periodAmount())->toBe(1050);
});

test('可以解析信用卡定期定額委託通知回傳資料', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->allows('decryptByAES')->andReturn([
        'Status' => 'SUCCESS',
        'Message' => '授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'MerchantOrderNo' => 'Order001',
            'TradeNo' => '20200101000000001',
            'AuthDate' => '2020-01-01 00:00:00',
            'TotalTimes' => 6,
            'AlreadyTimes' => 1,
            'AuthAmt' => 1050,
            'AuthCode' => '123456',
            'AuthBank' => 'CTBC',
            'NextAuthDate' => '2020-02-01',
            'PeriodNo' => '20200101000000001',
        ],
    ]);

    $result = NewebPay::periodNotify(Request::create('/callback', 'POST', [
        'Period' => 'encrypted_period_data',
    ]));

    expect($result)->toBeInstanceOf(NotifyResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->authAmount())->toBe(1050)
        ->and($result->periodNo())->toBe('20200101000000001');
});
