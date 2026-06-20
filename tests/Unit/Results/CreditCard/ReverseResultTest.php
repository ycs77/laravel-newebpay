<?php

use Ycs77\NewebPay\Results\CreditCard\ReverseResult;

test('ReverseResult → 解析取消交易結果', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '放棄授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 1050,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => 'Order001',
            'CheckCode' => '123456789',
        ],
    ];

    $result = new ReverseResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('放棄授權成功')
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050)
        ->and($result->checkCode())->toBe('123456789');
});
