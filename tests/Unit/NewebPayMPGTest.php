<?php

use Ycs77\LaravelRecoverSession\UserSource;
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
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/mpg_gateway');
});

test('NewebPay MPG sender is frontend', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getSender())->toBeInstanceOf(FrontendSender::class);
});

test('NewebPay MPG default TradeData', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->toBe([
        'MerchantID' => 'TestMerchantID1234',
        'TimeStamp' => 1577836800,
        'Version' => '2.0',
        'RespondType' => 'JSON',
        'LangType' => 'zh-tw',
        'TradeLimit' => 0,
        'ExpireDate' => '20200108',
        'ReturnURL' => 'http://localhost',
        'NotifyURL' => 'http://localhost',
        'CustomerURL' => 'http://localhost',
        'ClientBackURL' => 'http://localhost',
        'EmailModify' => 0,
        'LoginType' => 0,
        'OrderComment' => null,
        'CREDIT' => 1,
    ]);
});

test('NewebPay MPG credit', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

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
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('TokenTerm');
    expect($newebpay->tradeData())->not->toHaveKey('TokenTermDemand');

    config()->set('newebpay.payment_methods.credit_remember.enabled', CreditRememberDemand::EXPIRATION_DATE_AND_CVC);

    $newebpay->creditRemember('example_user');

    expect($newebpay->tradeData())->toHaveKey('TokenTerm', 'example_user');
    expect($newebpay->tradeData())->toHaveKey('TokenTermDemand', 1);
});

test('NewebPay MPG webATM', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('WEBATM');

    config()->set('newebpay.payment_methods.credit_remember.enabled', true);

    $newebpay->paymentMethods(['webATM' => true]);

    expect($newebpay->tradeData())->toHaveKey('WEBATM', 1);
});

test('NewebPay MPG ATM transfer (VACC)', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('VACC');

    $newebpay->paymentMethods(['VACC' => true]);

    expect($newebpay->tradeData())->toHaveKey('VACC', 1);
});

test('NewebPay MPG bank type', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('BankType');

    $newebpay->paymentMethods(['bank' => Bank::BOT]);

    expect($newebpay->tradeData())->toHaveKey('BankType', 'BOT');

    $newebpay->paymentMethods(['bank' => [Bank::BOT, Bank::HNCB]]);

    expect($newebpay->tradeData())->toHaveKey('BankType', 'BOT,HNCB');
});

test('NewebPay MPG NTCB', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

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
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('ANDROIDPAY');

    $newebpay->paymentMethods(['googlePay' => true]);

    expect($newebpay->tradeData())->toHaveKey('ANDROIDPAY', 1);
});

test('NewebPay MPG Samsung Pay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('SAMSUNGPAY');

    $newebpay->paymentMethods(['samsungPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('SAMSUNGPAY', 1);
});

test('NewebPay MPG LINE Pay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

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
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('UNIONPAY');

    $newebpay->paymentMethods(['unionPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('UNIONPAY', 1);
});

test('NewebPay MPG esunWallet', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('ESUNWALLET');

    $newebpay->paymentMethods(['esunWallet' => true]);

    expect($newebpay->tradeData())->toHaveKey('ESUNWALLET', 1);
});

test('NewebPay MPG TaiwanPay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('TAIWANPAY');

    $newebpay->paymentMethods(['taiwanPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('TAIWANPAY', 1);
});

test('NewebPay MPG ezPay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('EZPAY');

    $newebpay->paymentMethods(['ezPay' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPAY', 1);
});

test('NewebPay MPG ezpWeChat', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('EZPWECHAT');

    $newebpay->paymentMethods(['ezpWeChat' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPWECHAT', 1);
});

test('NewebPay MPG ezpAlipay', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('EZPALIPAY');

    $newebpay->paymentMethods(['ezpAlipay' => true]);

    expect($newebpay->tradeData())->toHaveKey('EZPALIPAY', 1);
});

test('NewebPay MPG CVS', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('CVS');

    $newebpay->paymentMethods(['CVS' => true]);

    expect($newebpay->tradeData())->toHaveKey('CVS', 1);
});

test('NewebPay MPG barcode', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('BARCODE');

    $newebpay->paymentMethods(['barcode' => true]);

    expect($newebpay->tradeData())->toHaveKey('BARCODE', 1);
});

test('NewebPay MPG CVSCOM', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('CVSCOM');

    $newebpay->CVSCOM(CVSCOM::PAY);

    expect($newebpay->tradeData())->toHaveKey('CVSCOM', CVSCOM::PAY->value);
});

test('NewebPay MPG LgsType', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), fakeUserSource());

    expect($newebpay->tradeData())->not->toHaveKey('LgsType');

    $newebpay->lgsType(LgsType::B2C);

    expect($newebpay->tradeData())->toHaveKey('LgsType', LgsType::B2C->value);
});

test('NewebPay MPG can be get request data', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    $requestData = $newebpay->requestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['TradeInfo'])->toBe('59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367dcd2f6b8bc5f400dd480e21977588750b56254eaa72b7a4f934c17316af8f3a5fa78f42692c2254b275051cc241cf1cc015366081d37c1c7eee766e03242194e3277b483247daa46c5ce80d04f5b1f1c3ef820fd671f745962f78c42bafb06439f59db0f5fa83e41bfa8ada59d6c84b27695445b6dd4d8b1278594054c119c7793a94662cc925004aad6404adf13df679f86a7c210b9a723b5e26cfba8e74cfc7bb62622f083a95971b19b8bca913b6af825304389cf5ac833ece4a6879c9930');
    expect($requestData['TradeSha'])->toBe('8A38D13011E8F9A1388561F401E5E0D1AACD0A346417F6AF2E634FB27B1D0288');
    expect($requestData['Version'])->toBe('2.0');
});

test('NewebPay MPG can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    $result = $newebpay
        ->order('TestNo123456', 100, '測試商品', 'test@email.com')
        ->submit();

    expect($result)->toBe('<!DOCTYPE html><html><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><form id="order-form" method="post" action="https://ccore.newebpay.com/MPG/mpg_gateway"><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367dcd2f6b8bc5f400dd480e21977588750b56254eaa72b7a4f934c17316af8f3a5fa78f42692c2254b275051cc241cf1cc015366081d37c1c7eee766e03242194e3277b483247daa46c5ce80d04f5b1f1c3ef820fd671f745962f78c42bafb06439f59db0f5fa83e41bfa8ada59d6c84b27695445b6dd4d8b1278594054c119c7793a94662cc925004aad6404adf13df679f86a7c210b9a723b5e26cfba8e74cfc615ac11d9e7990746613ae35b3386778f80f50ec7b1b38458c51fcf7a5c0bb26a48406b9093b18d01f0cd311272794d5553adee40ce16acc75a29444014adb37a7087f62d9676a951f1308dd05ab9003a6367d9207b235916586ee9ae85cc13447f1d21c56e6f75a9effd62fcf74870c55506679fabf725c7f29a8fb8e82d9ce"><input type="hidden" name="TradeSha" value="93E301B57B0DEE6E42D1BB3A4C24088ACAA0F28FAFFC88B3ABA058D052C61A4E"><input type="hidden" name="Version" value="2.0"></form><script>document.getElementById("order-form").submit();</script></body></html>');
});
