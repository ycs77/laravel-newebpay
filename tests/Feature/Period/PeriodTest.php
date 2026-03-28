<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Results\Period\AlterResult;
use Ycs77\NewebPay\Results\Period\AlterStatusResult;

use function Pest\Laravel\partialMock;

test('可以成功建立信用卡定期定額委託功能', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $response = NewebPay::period()
        ->create()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->submit();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->content())->toContain('action="https://ccore.newebpay.com/MPG/period"')
        ->and($response->content())->toContain('name="MerchantID_" value="TestMerchantID1234"')
        ->and($response->content())->toContain('name="PostData_" value="encrypted_data"');
});

test('可以成功修改委託狀態', function () {
    Http::fake([
        '*' => Http::response([
            // TODO: 暫時 pass 測試，之後要重構
            'Status' => 'SUCCESS',
            'period' => [
                'Status' => 'SUCCESS',
                'Message' => '修改成功',
                'Result' => [
                    'MerOrderNo' => 'Order001',
                    'PeriodNo' => '20200101000000001',
                    'AlterType' => 'terminate',
                    'NewNextTime' => '2020-02-01',
                ],
            ],
        ], 200),
    ]);

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $result = NewebPay::period()
        ->alterStatus()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->terminate();

    expect($result)->toBeInstanceOf(AlterStatusResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodStatus())->toBe(PeriodStatus::TERMINATE);
});

test('可以成功修改委託內容', function () {
    Http::fake([
        '*' => Http::response([
            // TODO: 暫時 pass 測試，之後要重構
            'Status' => 'SUCCESS',
            'Period' => [
                'Status' => 'SUCCESS',
                'Message' => '修改成功',
                'Result' => [
                    'MerOrderNo' => 'Order001',
                    'PeriodNo' => '20200101000000001',
                    'AlterAmt' => 1000,
                    'PeriodType' => 'D',
                    'PeriodPoint' => '3',
                    'NewNextAmt' => 1000,
                    'NewNextTime' => '2020-02-04',
                    'PeriodTimes' => 10,
                    'Extday' => '',
                ],
            ],
        ], 200),
    ]);

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $result = NewebPay::period()
        ->alter()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->everyFewDays(3)
        ->times(10)
        ->send();

    expect($result)->toBeInstanceOf(AlterResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodAmount())->toBe(1000);
});
