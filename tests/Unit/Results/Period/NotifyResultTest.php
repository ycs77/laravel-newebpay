<?php

use Ycs77\NewebPay\Results\Period\NotifyResult;

test('可以解析定期定額每期授權通知結果', function () {
    $data = [
        'Period' => [
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
                'EscrowBank' => 'HNCB',
                'AuthBank' => 'CTBC',
                'NextAuthDate' => '2020-02-01',
                'PeriodNo' => '20200101000000001',
            ],
        ],
    ];

    $result = new NotifyResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('20200101000000001')
        ->and($result->authDate())->toBe('2020-01-01 00:00:00')
        ->and($result->totalTimes())->toBe(6)
        ->and($result->alreadyTimes())->toBe(1)
        ->and($result->authAmount())->toBe(1050)
        ->and($result->authCode())->toBe('123456')
        ->and($result->escrowBank())->toBe('HNCB')
        ->and($result->authBank())->toBe('CTBC')
        ->and($result->nextAuthDate())->toBe('2020-02-01')
        ->and($result->periodNo())->toBe('20200101000000001');
});
