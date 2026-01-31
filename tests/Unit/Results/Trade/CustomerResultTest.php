<?php

use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\Trade\CustomerResult;

test('可以解析取號結果', function () {
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

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('條碼取號成功')
        ->and($result->result())->toBe($tradeData['Result'])
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->amount())->toBe(120)
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->orderNo())->toBe('1686763446')
        ->and($result->paymentType())->toBe(PaymentType::BARCODE)
        ->and($result->expireTime()?->format('Y-m-d H:i:s'))->toBe('2023-01-01 23:59:59');
});

test('可以解析 ATM 取號結果', function () {
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

    expect($result->paymentType())->toBe(PaymentType::VACC);
    expect($storeBarcode->bankCode())->toBe('007')
        ->and($storeBarcode->codeNo())->toBe('TestAccount12345');
});

test('可以解析代碼取號結果', function () {
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

    expect($result->paymentType())->toBe(PaymentType::CVS);
    expect($storeCode->codeNo())->toBe('TEST1234567890');
});

test('可以解析條碼取號結果', function () {
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

    expect($result->paymentType())->toBe(PaymentType::BARCODE);
    expect($storeBarcode->barcode1())->toBe('TEST1')
        ->and($storeBarcode->barcode2())->toBe('TEST2')
        ->and($storeBarcode->barcode3())->toBe('TEST3');
});

test('可以解析物流取號結果', function () {
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

    expect($result->paymentType())->toBe(PaymentType::CVSCOM);
    expect($lgs->storeCode())->toBe('019666')
        ->and($lgs->storeName())->toBe('全家台灣大道店')
        ->and($lgs->storeType())->toBe('全家')
        ->and($lgs->storeAddr())->toBe('台中市中區台灣大道一段531號')
        ->and($lgs->tradeType())->toBe(1)
        ->and($lgs->cvscomName())->toBe('Lucas Yang')
        ->and($lgs->cvscomPhone())->toBe('0900111222')
        ->and($lgs->lgsNo())->toBe('-')
        ->and($lgs->lgsType())->toBe('C2C');
});
