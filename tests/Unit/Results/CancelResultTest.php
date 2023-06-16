<?php

use Ycs77\NewebPay\Results\CancelResult;

test('the result data check code should be verified', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '放棄授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
        ],
    ];

    $result = new CancelResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    expect($result->verify())->toBeTrue();
});

test('can be get result data', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '放棄授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
        ],
    ];

    $result = new CancelResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    expect($result->status())->toBe('SUCCESS');
    expect($result->isSuccess())->toBeTrue();
    expect($result->isFail())->toBeFalse();
    expect($result->message())->toBe('放棄授權成功');
    expect($result->result())->toBe($data['Result']);
    expect($result->merchantId())->toBe('TestMerchantID1234');
    expect($result->amt())->toBe(120);
    expect($result->tradeNo())->toBe('23061500000000000');
    expect($result->merchantOrderNo())->toBe('1686759318');
});
