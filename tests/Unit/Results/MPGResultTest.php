<?php

use Illuminate\Http\Request;
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
            'AuthBank' => 'CTBC',
        ],
    ];

    $data = encryptTradeData($tradeData);

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
            'AuthBank' => 'CTBC',
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
            'AuthBank' => 'CTBC',
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
    expect($result->paymentType())->toBe('CREDIT');
    expect($credit->authBank())->toBe('CTBC');
    expect($credit->authBankName())->toBe('中國信託銀行');
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

test('can be get result data for ATM', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $atm = $result->atm();
    expect($result->paymentType())->toBe('VACC');
    expect($atm->payBankCode())->toBe(null);
    expect($atm->payerAccount5Code())->toBe('12345');
});

test('can be get result data for WebATM', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $atm = $result->atm();
    expect($result->paymentType())->toBe('WEBATM');
    expect($atm->payBankCode())->toBe('809');
    expect($atm->payerAccount5Code())->toBe('12345');
});

test('can be get result data for store code', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $storeCode = $result->storeCode();
    expect($result->paymentType())->toBe('CVS');
    expect($storeCode->codeNo())->toBe('TEST1234567890');
    expect($storeCode->storeType())->toBe(4);
    expect($storeCode->storeTypeName())->toBe('萊爾富');
    expect($storeCode->storeId())->toBe('S9999');
});

test('can be get result data for store barcode', function () {
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

    $result = new MPGResult([
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
    expect($storeBarcode->repayTimes())->toBe(0);
    expect($storeBarcode->payStore())->toBe('SEVEN');
    expect($storeBarcode->payStoreName())->toBe('7-11');
});

test('can be get result data for lgs', function () {
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

    $result = new MPGResult([
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

test('can be get result data for cross border', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $crossBorder = $result->crossBorder();
    expect($crossBorder->isCrossBorder())->toBeTrue();
    expect($crossBorder->channelId())->toBe('ALIPAY');
    expect($crossBorder->channelName())->toBe('支付寶');
    expect($crossBorder->channelNo())->toBe('NO0000000001');
});

test('can be get result data for EsunWallet', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $esunWallet = $result->esunWallet();
    expect($result->paymentType())->toBe('ESUNWALLET');
    expect($esunWallet->payAmt())->toBe(120);
    expect($esunWallet->redDisAmt())->toBe(0);
});

test('can be get result data for TaiwanPay', function () {
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

    $result = new MPGResult([
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => $tradeData,
        'TradeSha' => 'TradeSha',
        'Version' => '2.0',
    ]);

    $taiwanPay = $result->taiwanPay();
    expect($result->paymentType())->toBe('TAIWANPAY');
    expect($taiwanPay->payAmt())->toBe(120);
});
