<?php

use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\Trade\PaymentResult;

test('可以解析付款結果', function () {
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
            'AuthBank' => 'CTBC',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    expect($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('授權成功')
        ->and($result->result())->toBe($tradeData['Result'])
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->amount())->toBe(120)
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->orderNo())->toBe('1686759318')
        ->and($result->paymentType())->toBe(PaymentType::CREDIT)
        ->and($result->payTime()?->format('Y-m-d H:i:s'))->toBe('2023-01-01 00:00:00')
        ->and($result->ip())->toBe('127.0.0.1')
        ->and($result->escrowBank())->toBe('HNCB');
});

test('可以解析信用卡付款結果', function () {
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
            'AuthBank' => 'CTBC',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $credit = $result->credit();

    expect($result->paymentType())->toBe(PaymentType::CREDIT);
    expect($credit->authBank())->toBe('CTBC')
        ->and($credit->authBankName())->toBe('中國信託銀行')
        ->and($credit->respondCode())->toBe('00')
        ->and($credit->auth())->toBe('222111')
        ->and($credit->card6No())->toBe('400022')
        ->and($credit->card4No())->toBe('1111')
        ->and($credit->inst())->toBe(0)
        ->and($credit->instFirst())->toBe(0)
        ->and($credit->instEach())->toBe(0)
        ->and($credit->ECI())->toBe('')
        ->and($credit->tokenUseStatus())->toBe(0)
        ->and($credit->paymentMethod())->toBe('CREDIT');
});

test('可以解析 ATM 付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'PaymentType' => 'VACC',
            'PayTime' => '2023-01-01 00:00:00',
            'PayBankCode' => null,
            'PayerAccount5Code' => '12345',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $atm = $result->atm();

    expect($result->paymentType())->toBe(PaymentType::VACC);
    expect($atm->payBankCode())->toBe(null)
        ->and($atm->payerAccount5Code())->toBe('12345');
});

test('可以解析 WebATM 付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '付款完成',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'PaymentType' => 'WEBATM',
            'PayTime' => '2023-01-01 00:00:00',
            'PayBankCode' => '809',
            'PayerAccount5Code' => '12345',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $atm = $result->atm();

    expect($result->paymentType())->toBe(PaymentType::WEBATM);
    expect($atm->payBankCode())->toBe('809')
        ->and($atm->payerAccount5Code())->toBe('12345');
});

test('可以解析超商代碼付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '模擬付款成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'PaymentType' => 'CVS',
            'CodeNo' => 'TEST1234567890',
            'StoreType' => 4,
            'StoreID' => 'S9999',
            'PayTime' => '2023-01-01 00:00:00',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeCode = $result->storeCode();

    expect($result->paymentType())->toBe(PaymentType::CVS);
    expect($storeCode->codeNo())->toBe('TEST1234567890')
        ->and($storeCode->storeType())->toBe(4)
        ->and($storeCode->storeTypeName())->toBe('萊爾富')
        ->and($storeCode->storeId())->toBe('S9999');
});

test('可以解析超商條碼付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '模擬銷帳成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => 'HNCB',
            'PaymentType' => 'BARCODE',
            'Barcode_1' => 'TEST1',
            'Barcode_2' => 'TEST2',
            'Barcode_3' => 'TEST3',
            'RepayTimes' => 0,
            'PayStore' => 'SEVEN',
            'PayTime' => '2023-01-01 00:00:00',
        ],
    ];

    $result = new PaymentResult([
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
        ->and($storeBarcode->barcode3())->toBe('TEST3')
        ->and($storeBarcode->repayTimes())->toBe(0)
        ->and($storeBarcode->payStore())->toBe('SEVEN')
        ->and($storeBarcode->payStoreName())->toBe('7-11');
});

test('可以解析物流付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '訂單資料建立成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => '-',
            'PaymentType' => 'CVSCOM',
            'StoreCode' => '019666',
            'StoreType' => '全家',
            'StoreName' => '全家台灣大道店',
            'TradeType' => '1',
            'StoreAddr' => '台中市中區台灣大道一段531號',
            'CVSCOMName' => 'Lucas Yang',
            'CVSCOMPhone' => '0900111222',
            'LgsType' => 'C2C',
            'LgsNo' => '-',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $lgs = $result->lgs();

    expect($result->paymentType())->toBe(PaymentType::CVSCOM);
    expect($lgs->storeCode())->toBe('019666')
        ->and($lgs->storeType())->toBe('全家')
        ->and($lgs->storeName())->toBe('全家台灣大道店')
        ->and($lgs->storeAddr())->toBe('台中市中區台灣大道一段531號')
        ->and($lgs->tradeType())->toBe(1)
        ->and($lgs->cvscomName())->toBe('Lucas Yang')
        ->and($lgs->cvscomPhone())->toBe('0900111222')
        ->and($lgs->lgsType())->toBe('C2C')
        ->and($lgs->lgsNo())->toBe('-');
});

test('可以解析 ezPay 付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '訂單資料建立成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => '-',
            'ChannelID' => 'ALIPAY',
            'ChannelNo' => 'NO0000000001',
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $ezPay = $result->ezPay();

    expect($ezPay->isEzPay())->toBeTrue();
    expect($ezPay->channelId())->toBe('ALIPAY')
        ->and($ezPay->channelName())->toBe('支付寶')
        ->and($ezPay->channelNo())->toBe('NO0000000001');
});

test('可以解析玉山 Wallet 付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '訂單資料建立成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => '-',
            'PaymentType' => 'ESUNWALLET',
            'PayAmt' => 120,
            'RedDisAmt' => 0,
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $esunWallet = $result->esunWallet();

    expect($result->paymentType())->toBe(PaymentType::ESUNWALLET);
    expect($esunWallet->payAmt())->toBe(120)
        ->and($esunWallet->redDisAmt())->toBe(0);
});

test('可以解析台灣 Pay 付款結果', function () {
    $tradeData = [
        'Status' => 'SUCCESS',
        'Message' => '訂單資料建立成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686759318',
            'RespondType' => 'JSON',
            'IP' => '127.0.0.1',
            'EscrowBank' => '-',
            'PaymentType' => 'TAIWANPAY',
            'PayAmt' => 120,
        ],
    ];

    $result = new PaymentResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $taiwanPay = $result->taiwanPay();

    expect($result->paymentType())->toBe(PaymentType::TAIWANPAY);
    expect($taiwanPay->payAmt())->toBe(120);
});
