<?php

use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\Trade\QueryResult;

test('QueryResult → 解析付款查詢', function () {
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
            'CheckCode' => '123456789',
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

    $result = new QueryResult($data);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->amount())->toBe(120)
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->orderNo())->toBe('1686759318')
        ->and($result->paymentType())->toBe(PaymentType::CREDIT)
        ->and($result->createTime()->format('Y-m-d H:i:s'))->toBe('2023-01-01 00:00:00')
        ->and($result->payTime()?->format('Y-m-d H:i:s'))->toBe('2023-01-01 00:00:00');
});

test('QueryResult → 解析信用卡付款查詢', function () {
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
            'CheckCode' => '123456789',
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

    $result = new QueryResult($data);

    $credit = $result->credit();

    expect($credit->respondCode())->toBe('00')
        ->and($credit->auth())->toBe('222111')
        ->and($credit->ECI())->toBe('')
        ->and($credit->closeAmt())->toBe(120)
        ->and($credit->closeStatus())->toBe(0)
        ->and($credit->backBalance())->toBe(120)
        ->and($credit->backStatus())->toBe(0)
        ->and($credit->respondMsg())->toBe('授權測試')
        ->and($credit->inst())->toBe(0)
        ->and($credit->instFirst())->toBe(0)
        ->and($credit->instEach())->toBe(0)
        ->and($credit->paymentMethod())->toBe('CREDIT')
        ->and($credit->card6No())->toBe('400022')
        ->and($credit->card4No())->toBe('1111')
        ->and($credit->authBank())->toBe('CTBC')
        ->and($credit->authBankName())->toBe('中國信託銀行');
});
