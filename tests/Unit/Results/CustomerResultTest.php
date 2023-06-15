<?php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\NewebPayCustomer;
use Ycs77\NewebPay\Results\CustomerResult;

test('can be get customer result data', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '條碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'BARCODE',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'Barcode_1' => 'TEST1',
            'Barcode_2' => 'TEST2',
            'Barcode_3' => 'TEST3',
        ],
    ];

    $data = NewebPay::encryptTradeDataForTesting($tradeData);

    $request = Request::create('/customer', 'POST', [
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $data['TradeInfo'],
        'TradeSha' => $data['TradeSha'],
        'Version' => '2.0',
    ]);

    $newebpayResult = new NewebPayCustomer(app('config'), app('session.store'));

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
        'Message' => '條碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => '120',
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'BARCODE',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'Barcode_1' => 'TEST1',
            'Barcode_2' => 'TEST2',
            'Barcode_3' => 'TEST3',
        ],
    ];

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    expect($result->status())->toBe('SUCCESS');
    expect($result->isSuccess())->toBeTrue();
    expect($result->isFail())->toBeFalse();
    expect($result->message())->toBe('條碼取號成功');
    expect($result->result())->toBe($tradeData['Result']);
    expect($result->merchantId())->toBe('TestMerchantID1234');
    expect($result->amt())->toBe(120);
    expect($result->tradeNo())->toBe('23061500000000000');
    expect($result->merchantOrderNo())->toBe('1686763446');
    expect($result->paymentType())->toBe('BARCODE');
    expect($result->expireDate())->toBe('2023-01-01');
    expect($result->expireTime())->toBe('23:59:59');
});

test('can be get result data for ATM')->todo();

test('can be get result data for store code')->todo();

test('can be get result data for store barcode', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '條碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => '120',
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'BARCODE',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'Barcode_1' => 'TEST1',
            'Barcode_2' => 'TEST2',
            'Barcode_3' => 'TEST3',
        ],
    ];

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeBarcode = $result->storeBarcode();
    expect($storeBarcode->barcode1())->toBe('TEST1');
    expect($storeBarcode->barcode2())->toBe('TEST2');
    expect($storeBarcode->barcode3())->toBe('TEST3');
});

test('can be get result data for lgs')->todo();
