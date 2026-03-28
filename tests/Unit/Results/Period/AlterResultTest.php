<?php

use Ycs77\NewebPay\Enums\PeriodType;
use Ycs77\NewebPay\Results\Period\AlterResult;

test('可以解析修改委託金額結果', function () {
    $data = [
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
                'Extday' => '2501',
            ],
        ],
    ];

    $result = new AlterResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('修改成功')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodNo())->toBe('20200101000000001')
        ->and($result->periodAmount())->toBe(1000)
        ->and($result->periodType())->toBe(PeriodType::EVERY_FEW_DAYS)
        ->and($result->periodPoint())->toBe('3')
        ->and($result->newNextAmount())->toBe(1000)
        ->and($result->newNextTime())->toBe('2020-02-04')
        ->and($result->periodTimes())->toBe(10)
        ->and($result->creditExpiredAt()->format('Y-m'))->toBe('2025-01');
});
