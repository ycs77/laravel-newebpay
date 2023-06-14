<?php

use Mockery as m;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\NewebPayMPG;
use Ycs77\NewebPay\Senders\FrontendSender;

beforeEach(function () {
    setTestNow();
});

test('NewebPay MPG can be get url', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/mpg_gateway');
});

test('NewebPay MPG sender is frontend', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(FrontendSender::class);
});

test('NewebPay MPG default TradeData', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->toBe([
        'MerchantID' => 'TestMerchantID1234',
        'TimeStamp' => 1577836800,
        'Version' => '2.0',
        'RespondType' => 'JSON',
        'LangType' => 'zh-tw',
        'TradeLimit' => 0,
        'ExpireDate' => '20200108',
        'ReturnURL' => 'http://localhost/pay/callback',
        'NotifyURL' => 'http://localhost/pay/notify',
        'CustomerURL' => 'http://localhost/pay/customer',
        'EmailModify' => 0,
        'LoginType' => 0,
        'OrderComment' => null,
        'CREDIT' => 1,
    ]);
});

test('NewebPay MPG credit', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->toHaveKey('CREDIT', 1);
    expect($newebpay->tradeData())->not->toHaveKey('CreditRed');
    expect($newebpay->tradeData())->not->toHaveKey('InstFlag');

    $newebpay->paymentMethods([
        'credit' => [
            'enabled' => true,
            'red' => true,
            'inst' => CreditInst::NONE,
        ],
    ]);
    expect($newebpay->tradeData())->toHaveKey('CreditRed', 1);

    $newebpay->paymentMethods([
        'credit' => [
            'enabled' => true,
            'red' => true,
            'inst' => CreditInst::P3,
        ],
    ]);
    expect($newebpay->tradeData())->toHaveKey('InstFlag', '3');

    $newebpay->paymentMethods([
        'credit' => [
            'enabled' => true,
            'red' => true,
            'inst' => [CreditInst::P3, CreditInst::P6, CreditInst::P12],
        ],
    ]);
    expect($newebpay->tradeData())->toHaveKey('InstFlag', '3,6,12');
});

test('NewebPay MPG credit remember', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('TokenTerm');
    expect($newebpay->tradeData())->not->toHaveKey('TokenTermDemand');

    config()->set('newebpay.payment_methods.credit_remember.enabled', CreditRememberDemand::EXPIRATION_DATE_AND_CVC);

    $newebpay->creditRemember('example_user');

    expect($newebpay->tradeData())->toHaveKey('TokenTerm', 'example_user');
    expect($newebpay->tradeData())->toHaveKey('TokenTermDemand', 1);
});

test('NewebPay MPG webATM', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('WEBATM');

    config()->set('newebpay.payment_methods.credit_remember.enabled', true);

    $newebpay->paymentMethods(['webATM' => true]);

    expect($newebpay->tradeData())->toHaveKey('WEBATM', 1);
});

test('NewebPay MPG ATM transfer (VACC)', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('VACC');

    $newebpay->paymentMethods(['VACC' => true]);

    expect($newebpay->tradeData())->toHaveKey('VACC', 1);
});

test('NewebPay MPG bank type', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('BankType');

    $newebpay->paymentMethods(['bank' => Bank::BOT]);

    expect($newebpay->tradeData())->toHaveKey('BankType', 'BOT');

    $newebpay->paymentMethods(['bank' => [Bank::BOT, Bank::HNCB]]);

    expect($newebpay->tradeData())->toHaveKey('BankType', 'BOT,HNCB');
});

test('NewebPay MPG NTCB', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('NTCB');
    expect($newebpay->tradeData())->not->toHaveKey('NTCBLocate');
    expect($newebpay->tradeData())->not->toHaveKey('NTCBStartDate');
    expect($newebpay->tradeData())->not->toHaveKey('NTCBEndDate');

    $newebpay->paymentMethods([
        'NTCB' => [
            'enabled' => true,
            'locate' => NTCBLocate::HsinchuCity,
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-01',
        ],
    ]);

    expect($newebpay->tradeData())->toHaveKey('NTCB', 1);
    expect($newebpay->tradeData())->toHaveKey('NTCBLocate', NTCBLocate::HsinchuCity->value);
    expect($newebpay->tradeData())->toHaveKey('NTCBStartDate', '2020-01-01');
    expect($newebpay->tradeData())->toHaveKey('NTCBEndDate', '2020-01-01');
});

test('NewebPay MPG Google Pay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('ANDROIDPAY');

    $newebpay->paymentMethods(['googlePay' => true]);

    expect($newebpay->tradeData())->toHaveKey('ANDROIDPAY', 1);
});

test('NewebPay MPG Samsung Pay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('SAMSUNGPAY');

    $newebpay->paymentMethods(['samsungPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('SAMSUNGPAY', 1);
});

test('NewebPay MPG LINE Pay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('LINEPAY');
    expect($newebpay->tradeData())->not->toHaveKey('ImageUrl');

    $newebpay->paymentMethods([
        'linePay' => [
            'enabled' => true,
            'image_url' => 'http://example.com/your-image-url',
        ],
    ]);

    expect($newebpay->tradeData())->toHaveKey('LINEPAY', 1);
    expect($newebpay->tradeData())->toHaveKey('ImageUrl', 'http://example.com/your-image-url');
});

test('NewebPay MPG UnionPay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('UNIONPAY');

    $newebpay->paymentMethods(['unionPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('UNIONPAY', 1);
});

test('NewebPay MPG esunWallet', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('ESUNWALLET');

    $newebpay->paymentMethods(['esunWallet' => true]);

    expect($newebpay->tradeData())->toHaveKey('ESUNWALLET', 1);
});

test('NewebPay MPG TaiwanPay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('TAIWANPAY');

    $newebpay->paymentMethods(['taiwanPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('TAIWANPAY', 1);
});

test('NewebPay MPG ezPay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('EZPAY');

    $newebpay->paymentMethods(['ezPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPAY', 1);
});

test('NewebPay MPG ezpWeChat', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('EZPWECHAT');

    $newebpay->paymentMethods(['ezpWeChat' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPWECHAT', 1);
});

test('NewebPay MPG ezpAlipay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('EZPALIPAY');

    $newebpay->paymentMethods(['ezpAlipay' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPALIPAY', 1);
});

test('NewebPay MPG CVS', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('CVS');

    $newebpay->paymentMethods(['CVS' => true]);

    expect($newebpay->tradeData())->toHaveKey('CVS', 1);
});

test('NewebPay MPG barcode', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('BARCODE');

    $newebpay->paymentMethods(['barcode' => true]);

    expect($newebpay->tradeData())->toHaveKey('BARCODE', 1);
});

test('NewebPay MPG CVSCOM', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('CVSCOM');

    $newebpay->CVSCOM(CVSCOM::PAY);

    expect($newebpay->tradeData())->toHaveKey('CVSCOM', CVSCOM::PAY->value);
});

test('NewebPay MPG LgsType', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->tradeData())->not->toHaveKey('LgsType');

    $newebpay->lgsType(LgsType::B2C);

    expect($newebpay->tradeData())->toHaveKey('LgsType', LgsType::B2C->value);
});

test('NewebPay MPG can be get request data', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    $requestData = $newebpay->requestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['TradeInfo'])->toBe('59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367dcd2f6b8bc5f400dd480e21977588750b56254eaa72b7a4f934c17316af8f3a554f7558f87d1b4f087fdcb6c55175c83bfb9525bf2f0689a06c451e480d66689b0e058a137d7c837af2bfb59925b603b68beedf7d6ed1bac4775350f43ee8205504062dd88987473aca1a941cb9cbf8f2c82224eec9ed5a933abbe527d2a616440b8dfaf091917768023aa0d1f5aef0a50e722bb3906358987c19cb2ffc058eb11fba7b46e096a0664c9eb11701df96c50a28800848f7955051f41353e1e6df2');
    expect($requestData['TradeSha'])->toBe('C0B415098C8CFD21AADA1AD50D5AAFAC26A9CEEA4DB0BC072FB4E0BE482B268B');
    expect($requestData['Version'])->toBe('2.0');
});

test('NewebPay MPG can be submit', function () {
    setTestNow();

    /** @var \Ycs77\NewebPay\Senders\FrontendSender|\Mockery\MockInterface|\Mockery\LegacyMockInterface */
    $sender = m::mock(FrontendSender::class);
    $sender->makePartial();
    $sender->shouldReceive('preserveUserSource');

    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    $result = $newebpay
        ->order('TestNo123456', 100, '測試商品', 'test@email.com')
        ->setSender($sender)
        ->submit();

    expect($result)->toBe('<!DOCTYPE html><html><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><form id="order-form" method="post" action="https://ccore.newebpay.com/MPG/mpg_gateway"><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367dcd2f6b8bc5f400dd480e21977588750b56254eaa72b7a4f934c17316af8f3a554f7558f87d1b4f087fdcb6c55175c83bfb9525bf2f0689a06c451e480d66689b0e058a137d7c837af2bfb59925b603b68beedf7d6ed1bac4775350f43ee8205504062dd88987473aca1a941cb9cbf8f2c82224eec9ed5a933abbe527d2a616440b8dfaf091917768023aa0d1f5aef0a50e722bb3906358987c19cb2ffc058eb11fba7b46e096a0664c9eb11701df96c38f41dfb6ee2714eaa5d5db73b729a1dbed0f1594e0fdb35deda27a8e9394a045c01557a01ab1f085002454e39d25abedea5565e3c44ee384db3ec6cde7e3588b1f2c183b2fa3cf152851b775a473b8af2fef09b3b59444540356e3041effb52104f2650afcc58ebf7f362b11f6166e864ce3a27bcf6635af5eeeb9ee0480898cde35b5e60b05afca577f5ee76c524e1"><input type="hidden" name="TradeSha" value="DB9965052554FAAB09E042D8252708D85FC35D71A74BF848E75D20FBBD1209C0"><input type="hidden" name="Version" value="2.0"></form><script>document.getElementById("order-form").submit();</script></body></html>');
});
