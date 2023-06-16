<?php

use Ycs77\NewebPay\Results\CloseResult;

test('can be get result data', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '請款資料新增成功_模擬信用卡請款成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
        ],
    ];

    $result = new CloseResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    expect($result->status())->toBe('SUCCESS');
    expect($result->isSuccess())->toBeTrue();
    expect($result->isFail())->toBeFalse();
    expect($result->message())->toBe('請款資料新增成功_模擬信用卡請款成功');
    expect($result->result())->toBe($data['Result']);
    expect($result->merchantId())->toBe('TestMerchantID1234');
    expect($result->amt())->toBe(120);
    expect($result->tradeNo())->toBe('23061500000000000');
    expect($result->merchantOrderNo())->toBe('1686759318');
});
