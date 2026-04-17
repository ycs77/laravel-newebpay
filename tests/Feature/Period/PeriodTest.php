<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Period\AlterOptions;
use Ycs77\NewebPay\Options\Period\AlterStatusOptions;
use Ycs77\NewebPay\Resources\Period;
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
            'period' => 'encrypted_period_data',
        ], 200),
    ]);

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');
    $crypto->allows('decryptByAES')->andReturn([
        'Status' => 'SUCCESS',
        'Message' => '修改成功',
        'Result' => [
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterType' => 'terminate',
            'NewNextTime' => '2020-02-01',
        ],
    ]);

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
            'Period' => 'encrypted_period_data',
        ], 200),
    ]);

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_data');
    $crypto->allows('decryptByAES')->andReturn([
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
    ]);

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

test('信用卡定期定額委託 → 模擬修改委託金額', function () {
    NewebPay::fake([
        AlterResult::make([
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
        ]),
    ]);

    $result = NewebPay::period()
        ->alter()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withAmount(1000)
        ->everyFewDays(3)
        ->times(10)
        ->send();

    NewebPay::assertSent(Period::class, 'alter', function (AlterOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->alterAmt === 1000;
    });

    expect($result)->toBeInstanceOf(AlterResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodAmount())->toBe(1000);
});

test('信用卡定期定額委託 → 模擬暫停委託', function () {
    NewebPay::fake([
        AlterStatusResult::make([
            'period' => [
                'Status' => 'SUCCESS',
                'Message' => '修改成功',
                'Result' => [
                    'MerOrderNo' => 'Order001',
                    'PeriodNo' => '20200101000000001',
                    'AlterType' => 'suspend',
                    'NewNextTime' => '2020-02-01',
                ],
            ],
        ]),
    ]);

    $result = NewebPay::period()
        ->alterStatus()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->suspend();

    NewebPay::assertSent(Period::class, 'alterStatus', function (AlterStatusOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->alterType === PeriodStatus::SUSPEND;
    });

    expect($result)->toBeInstanceOf(AlterStatusResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodStatus())->toBe(PeriodStatus::SUSPEND);
});

test('信用卡定期定額委託 → 模擬終止委託', function () {
    NewebPay::fake([
        AlterStatusResult::make([
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
        ]),
    ]);

    $result = NewebPay::period()
        ->alterStatus()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->terminate();

    NewebPay::assertSent(Period::class, 'alterStatus', function (AlterStatusOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->alterType === PeriodStatus::TERMINATE;
    });

    expect($result)->toBeInstanceOf(AlterStatusResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodStatus())->toBe(PeriodStatus::TERMINATE);
});

test('信用卡定期定額委託 → 模擬恢復委託', function () {
    NewebPay::fake([
        AlterStatusResult::make([
            'period' => [
                'Status' => 'SUCCESS',
                'Message' => '修改成功',
                'Result' => [
                    'MerOrderNo' => 'Order001',
                    'PeriodNo' => '20200101000000001',
                    'AlterType' => 'restart',
                    'NewNextTime' => '2020-02-01',
                ],
            ],
        ]),
    ]);

    $result = NewebPay::period()
        ->alterStatus()
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->resume();

    NewebPay::assertSent(Period::class, 'alterStatus', function (AlterStatusOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->alterType === PeriodStatus::RESTART;
    });

    expect($result)->toBeInstanceOf(AlterStatusResult::class)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodStatus())->toBe(PeriodStatus::RESTART);
});
