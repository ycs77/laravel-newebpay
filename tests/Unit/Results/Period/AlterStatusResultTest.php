<?php

use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Results\Period\AlterStatusResult;

test('可以解析修改委託狀態結果', function () {
    $data = [
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
    ];

    $result = new AlterStatusResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('修改成功')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->periodNo())->toBe('20200101000000001')
        ->and($result->periodStatus())->toBe(PeriodStatus::TERMINATE)
        ->and($result->newNextTime())->toBe('2020-02-01');
});

test('可以解析暫停委託狀態結果', function () {
    $data = [
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
    ];

    $result = new AlterStatusResult($data);

    expect($result->periodStatus())->toBe(PeriodStatus::SUSPEND);
});

test('可以解析重啟委託狀態結果', function () {
    $data = [
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
    ];

    $result = new AlterStatusResult($data);

    expect($result->periodStatus())->toBe(PeriodStatus::RESTART);
});
