<?php

use Ycs77\NewebPay\Results\CreditCard\CaptureResult;

test('可以解析信用卡請款結果', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '請款資料新增成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 1050,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => 'Order001',
        ],
    ];

    $result = new CaptureResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('請款資料新增成功')
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});
