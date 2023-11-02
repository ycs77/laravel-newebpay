<?php

use Ycs77\NewebPay\Results\QueryResult;

test('the result data check code should be verified', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '查詢成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'TradeStatus' => 1,
            'PaymentType' => 'CREDIT',
            'CreateTime' => '2023-01-01 00:00:00',
            'PayTime' => '2023-01-01 00:00:00',
            'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
            'FundTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'ECI' => '',
            'CloseAmt' => 120,
            'CloseStatus' => 0,
            'BackBalance' => 120,
            'BackStatus' => 0,
            'RespondMsg' => '授權測試',
            'Inst' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'PaymentMethod' => 'CREDIT',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'AuthBank' => 'CTBC',
        ],
    ];

    $result = new QueryResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    expect($result->verify())->toBeTrue();
});

test('can be get result data for all payment methods', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '查詢成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'TradeStatus' => 1,
            'PaymentType' => 'CREDIT',
            'CreateTime' => '2023-01-01 00:00:00',
            'PayTime' => '2023-01-01 00:00:00',
            'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
            'FundTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'ECI' => '',
            'CloseAmt' => 120,
            'CloseStatus' => 0,
            'BackBalance' => 120,
            'BackStatus' => 0,
            'RespondMsg' => '授權測試',
            'Inst' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'PaymentMethod' => 'CREDIT',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'AuthBank' => 'CTBC',
        ],
    ];

    $result = new QueryResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    expect($result->status())->toBe('SUCCESS');
    expect($result->isSuccess())->toBeTrue();
    expect($result->isFail())->toBeFalse();
    expect($result->message())->toBe('查詢成功');
    expect($result->result())->toBe($data['Result']);
    expect($result->merchantId())->toBe('TestMerchantID1234');
    expect($result->amt())->toBe(120);
    expect($result->tradeNo())->toBe('23061500000000000');
    expect($result->merchantOrderNo())->toBe('1686759318');
    expect($result->paymentType())->toBe('CREDIT');
    expect($result->createTime())->toBe('2023-01-01 00:00:00');
    expect($result->payTime())->toBe('2023-01-01 00:00:00');
});

test('can be get result data for credit', function () {
    $data = [
        'Status' => 'SUCCESS',
        'Message' => '查詢成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'TradeStatus' => 1,
            'PaymentType' => 'CREDIT',
            'CreateTime' => '2023-01-01 00:00:00',
            'PayTime' => '2023-01-01 00:00:00',
            'CheckCode' => 'D9D7DE14F1AE19AACAD326C199D8E0C96EE8DFAED06FB3AA6BE379A3EB2164AE',
            'FundTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'ECI' => '',
            'CloseAmt' => 120,
            'CloseStatus' => 0,
            'BackBalance' => 120,
            'BackStatus' => 0,
            'RespondMsg' => '授權測試',
            'Inst' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'PaymentMethod' => 'CREDIT',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'AuthBank' => 'CTBC',
        ],
    ];

    $result = new QueryResult($data, config('newebpay.hash_key'), config('newebpay.hash_iv'));

    $credit = $result->credit();
    expect($credit->respondCode())->toBe('00');
    expect($credit->auth())->toBe('222111');
    expect($credit->ECI())->toBe('');
    expect($credit->closeAmt())->toBe(120);
    expect($credit->closeStatus())->toBe(0);
    expect($credit->backBalance())->toBe(120);
    expect($credit->backStatus())->toBe(0);
    expect($credit->respondMsg())->toBe('授權測試');
    expect($credit->inst())->toBe(0);
    expect($credit->instFirst())->toBe(0);
    expect($credit->instEach())->toBe(0);
    expect($credit->paymentMethod())->toBe('CREDIT');
    expect($credit->card6No())->toBe('400022');
    expect($credit->card4No())->toBe('1111');
    expect($credit->authBank())->toBe('CTBC');
    expect($credit->authBankName())->toBe('中國信託銀行');
});
