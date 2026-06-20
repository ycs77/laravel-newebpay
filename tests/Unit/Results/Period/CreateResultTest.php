<?php

use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Results\Period\CreateResult;

test('CreateResult → 解析委託結果（不含授權資料）', function () {
    $data = [
        'Period' => [
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
        ],
    ];

    $result = new CreateResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodType())->toBe(PeriodType::MONTHLY)
        ->and($result->authTimes())->toBe(6)
        ->and($result->dateArray())->toHaveCount(6)
        ->and($result->periodAmount())->toBe(1050)
        ->and($result->periodNo())->toBe('20200101000000001');
});

test('CreateResult → 解析委託結果（含授權資料）', function () {
    $data = [
        'Period' => [
            'Status' => 'SUCCESS',
            'Message' => '委託建立成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'MerchantOrderNo' => 'Order001',
                'PeriodType' => 'D',
                'AuthTimes' => 3,
                'DateArray' => '2020-01-03,2020-01-05,2020-01-07',
                'PeriodAmt' => 1050,
                'PeriodNo' => '20200101000000001',
                'AuthTime' => '2020-01-01 00:00:00',
                'TradeNo' => '20200101000000001',
                'CardNo' => '400022xxxx1111',
                'AuthCode' => '123456',
                'RespondCode' => '00',
            ],
        ],
    ];

    $result = new CreateResult($data);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->periodType())->toBe(PeriodType::EVERY_FEW_DAYS)
        ->and($result->authTime())->toBe('2020-01-01 00:00:00')
        ->and($result->tradeNo())->toBe('20200101000000001')
        ->and($result->cardNo())->toBe('400022xxxx1111')
        ->and($result->authCode())->toBe('123456')
        ->and($result->respondCode())->toBe('00');
});

test('CreateResult → 判斷委託建立失敗', function () {
    $data = [
        'Period' => [
            'Status' => 'MPG10001',
            'Message' => '委託建立失敗',
            'Result' => [],
        ],
    ];

    $result = new CreateResult($data);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->isFail())->toBeTrue()
        ->and($result->message())->toBe('委託建立失敗');
});
