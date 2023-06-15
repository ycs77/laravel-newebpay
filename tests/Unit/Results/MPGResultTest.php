<?php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\NewebPayResult;
use Ycs77\NewebPay\Results\MPGResult;

test('can be get result data on callback request', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'ItemDesc' => '我的商品',
            'PaymentType' => 'CREDIT',
            'PayTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'Exp' => '6405',
            'TokenUseStatus' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'Inst' => 0,
            'ECI' => '',
            'PaymentMethod' => 'CREDIT',
            'AuthBank' => 'KGI',
        ],
    ];

    $data = NewebPay::encryptTradeDataForTesting($tradeData);

    $request = Request::create('/callback', 'POST', [
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $data['TradeInfo'],
        'TradeSha' => $data['TradeSha'],
        'Version' => '2.0',
    ]);

    $newebpayResult = new NewebPayResult(app('config'), app('session.store'));

    $result = $newebpayResult->result($request);

    expect($result->data())->toBe([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => $data['TradeSha'],
        'Version' => '2.0',
    ]);
});

test('can be get result data for all payment methods', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'ItemDesc' => '我的商品',
            'PaymentType' => 'CREDIT',
            'PayTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'Exp' => '6405',
            'TokenUseStatus' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'Inst' => 0,
            'ECI' => '',
            'PaymentMethod' => 'CREDIT',
            'AuthBank' => 'KGI',
        ],
    ];

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    expect($result->status())->toBe('SUCCESS');
    expect($result->isSuccess())->toBeTrue();
    expect($result->isFail())->toBeFalse();
    expect($result->message())->toBe('授權成功');
    expect($result->result())->toBe($tradeData['Result']);
    expect($result->merchantId())->toBe('TestMerchantID1234');
    expect($result->amt())->toBe(120);
    expect($result->tradeNo())->toBe('23061500000000000');
    expect($result->merchantOrderNo())->toBe('1686759318');
    expect($result->paymentType())->toBe('CREDIT');
    expect($result->respondType())->toBe('JSON');
    expect($result->payTime())->toBe('2023-01-01 00:00:00');
    expect($result->ip())->toBe('127.0.0.1');
    expect($result->escrowBank())->toBe('HNCB');
});

test('can be get result data for credit', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '授權成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'ItemDesc' => '我的商品',
            'PaymentType' => 'CREDIT',
            'PayTime' => '2023-01-01 00:00:00',
            'RespondCode' => '00',
            'Auth' => '222111',
            'Card6No' => '400022',
            'Card4No' => '1111',
            'Exp' => '6405',
            'TokenUseStatus' => 0,
            'InstFirst' => 0,
            'InstEach' => 0,
            'Inst' => 0,
            'ECI' => '',
            'PaymentMethod' => 'CREDIT',
            'AuthBank' => 'KGI',
        ],
    ];

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $credit = $result->credit();
    expect($credit->authBank())->toBe('KGI');
    expect($credit->authBankText())->toBe('KGI');
    expect($credit->respondCode())->toBe('00');
    expect($credit->auth())->toBe('222111');
    expect($credit->card6No())->toBe('400022');
    expect($credit->card4No())->toBe('1111');
    expect($credit->inst())->toBe(0);
    expect($credit->instFirst())->toBe(0);
    expect($credit->instEach())->toBe(0);
    expect($credit->ECI())->toBe('');
    expect($credit->tokenUseStatus())->toBe(0);
    expect($credit->paymentMethod())->toBe('CREDIT');
});

test('can be get result data for ATM')->todo();

test('can be get result data for store code')->todo();

test('can be get result data for store barcode')->todo();

test('can be get result data for cross border')->todo();

test('can be get result data for EsunWallet')->todo();

test('can be get result data for TaiwanPay')->todo();
