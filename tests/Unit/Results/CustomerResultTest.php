<?php

use Illuminate\Http\Request;
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

    $data = encryptTradeData($tradeData);

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

test('can be get result data for ATM', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'VACC',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'BankCode' => '007',
            'CodeNo' => 'TestAccount12345',
        ],
    ];

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeBarcode = $result->atm();
    expect($result->paymentType())->toBe('VACC');
    expect($storeBarcode->bankCode())->toBe('007');
    expect($storeBarcode->codeNo())->toBe('TestAccount12345');
});

test('can be get result data for store code', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '代碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'CVS',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'CodeNo' => 'TEST1234567890',
        ],
    ];

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeCode = $result->storeCode();
    expect($result->paymentType())->toBe('CVS');
    expect($storeCode->codeNo())->toBe('TEST1234567890');
});

test('can be get result data for store barcode', function () {
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

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeBarcode = $result->storeBarcode();
    expect($result->paymentType())->toBe('BARCODE');
    expect($storeBarcode->barcode1())->toBe('TEST1');
    expect($storeBarcode->barcode2())->toBe('TEST2');
    expect($storeBarcode->barcode3())->toBe('TEST3');
});

test('can be get result data for lgs', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '條碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'CVSCOM',
            'RespondType' => 'JSON',
            'StoreCode' => '019666',
            'StoreName' => '全家台灣大道店',
            'TradeType' => '1',
            'StoreType' => '全家',
            'CVSCOMName' => 'Lucas Yang',
            'CVSCOMPhone' => '0900111222',
            'StoreAddr' => '台中市中區台灣大道一段531號',
            'LgsType' => 'C2C',
            'LgsNo' => '-',
        ],
    ];

    $result = new CustomerResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $lgs = $result->lgs();
    expect($result->paymentType())->toBe('CVSCOM');
    expect($lgs->storeCode())->toBe('019666');
    expect($lgs->storeName())->toBe('全家台灣大道店');
    expect($lgs->storeType())->toBe('全家');
    expect($lgs->storeAddr())->toBe('台中市中區台灣大道一段531號');
    expect($lgs->tradeType())->toBe(1);
    expect($lgs->cvscomName())->toBe('Lucas Yang');
    expect($lgs->cvscomPhone())->toBe('0900111222');
    expect($lgs->lgsNo())->toBe('-');
    expect($lgs->lgsType())->toBe('C2C');
});
