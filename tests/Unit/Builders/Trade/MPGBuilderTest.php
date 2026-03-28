<?php

use Carbon\Carbon;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Trade\MPGBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $this->factory = mock(Factory::class);
    $this->factory->allows('baseUrl')->andReturn('https://example.com');

    $this->config = [
        'merchant_id' => 'TestMerchantID1234',
        'hash_key' => 'TestHashKey123456789',
        'hash_iv' => '17ef14e533ed1c18',
        'lang' => LangType::ZH_TW,
        'return_url' => '/pay/callback',
        'notify_url' => '/pay/notify',
        'customer_url' => '/pay/customer',
        'client_back_url' => null,
        'payment_methods' => [
            'credit' => [
                'enabled' => true,
                'red' => false,
                'inst' => CreditInst::NONE,
            ],
            'webATM' => false,
            'VACC' => false,
            'bank' => Bank::ALL,
            'NTCB' => [
                'enabled' => false,
                'locate' => NTCBLocate::TaipeiCity,
                'start_date' => '2015-01-01',
                'end_date' => '2015-01-01',
            ],
            'googlePay' => false,
            'samsungPay' => false,
            'linePay' => [
                'enabled' => false,
            ],
            'unionPay' => false,
            'esunWallet' => false,
            'taiwanPay' => false,
            'ezPay' => false,
            'ezpWeChat' => false,
            'ezpAlipay' => false,
            'CVS' => false,
            'barcode' => false,
        ],
        'timeout' => 30,
    ];

    $this->crypto = mock(Crypto::class);
    $this->crypto->expects('setHashKey');
    $this->crypto->expects('setHashIv');
    $this->crypto->expects('encryptByAES')->andReturn('encrypted_data');
    $this->crypto->expects('hashBySHA')->andReturn('encrypted_data');

    $this->httpTransporter = mock(HttpTransporter::class);

    $this->response = new Response('<div>redirect form</div>');

    $this->formRedirectTransporter = mock(FormRedirectTransporter::class);
    $this->formRedirectTransporter->expects('send')->andReturn($this->response);
});

test('可以成功呼叫 MPG 金流基本功能', function () {
    $expectedTradeInfoData = [
        'MerchantID' => 'TestMerchantID1234',
        'RespondType' => 'JSON',
        'TimeStamp' => Carbon::now()->timestamp,
        'Version' => '2.0',
        'LangType' => 'zh-tw',
        'MerchantOrderNo' => 'Order001',
        'Amt' => 1050,
        'ItemDesc' => '測試商品',
        'ReturnURL' => 'http://localhost/pay/callback',
        'NotifyURL' => 'http://localhost/pay/notify',
        'CustomerURL' => 'http://localhost/pay/customer',
        'Email' => 'customer@example.com',
        'CREDIT' => 1,
    ];

    $response = (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->onPreparedOptions(function (Options $options) use ($expectedTradeInfoData) {
            expect($options->toArray()['TradeInfo'])->toBe($expectedTradeInfoData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('MPG 信用卡 預設值', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->not->toHaveKey('CreditRed');
            expect($tradeInfo)->not->toHaveKey('InstFlag');
        })
        ->submit();
});

test('MPG 信用卡 啟用紅利交易', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods([
            'credit' => [
                'enabled' => true,
                'red' => true,
                'inst' => CreditInst::NONE,
            ],
        ])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CreditRed', 1);
        })
        ->submit();
});

test('MPG 信用卡 啟用單個分期付款選項', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods([
            'credit' => [
                'enabled' => true,
                'red' => true,
                'inst' => CreditInst::P3,
            ],
        ])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('InstFlag', '3');
        })
        ->submit();
});

test('MPG 信用卡 啟用多個分期付款選項', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods([
            'credit' => [
                'enabled' => true,
                'red' => true,
                'inst' => [CreditInst::P3, CreditInst::P6, CreditInst::P12],
            ],
        ])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('InstFlag', '3,6,12');
        })
        ->submit();
});

test('MPG 信用卡 記憶卡號', function () {
    config()->set('newebpay.payment_methods.credit_remember.enabled', CreditRememberDemand::EXPIRATION_DATE_AND_CVC);

    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCreditRemember('example_user')
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('TokenTerm', 'example_user');
            expect($tradeInfo)->toHaveKey('TokenTermDemand', 1);
        })
        ->submit();
});

test('MPG 啟用 webATM', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['webATM' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('WEBATM', 1);
        })
        ->submit();
});

test('MPG 啟用 ATM 轉帳 (VACC)', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['VACC' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('VACC', 1);
        })
        ->submit();
});

test('MPG 啟用單個銀行選項', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['bank' => Bank::BOT])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BankType', 'BOT');
        })
        ->submit();
});

test('MPG 啟用多個銀行選項', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['bank' => [Bank::BOT, Bank::HNCB]])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BankType', 'BOT,HNCB');
        })
        ->submit();
});

test('MPG 啟用 NTCB', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods([
            'NTCB' => [
                'enabled' => true,
                'locate' => NTCBLocate::HsinchuCity,
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-01',
            ],
        ])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('NTCB', 1);
            expect($tradeInfo)->toHaveKey('NTCBLocate', '005');
            expect($tradeInfo)->toHaveKey('NTCBStartDate', '2020-01-01');
            expect($tradeInfo)->toHaveKey('NTCBEndDate', '2020-01-01');
        })
        ->submit();
});

test('MPG 啟用 Google Pay', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['googlePay' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('ANDROIDPAY', 1);
        })
        ->submit();
});

test('MPG 啟用 Samsung Pay', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['samsungPay' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('SAMSUNGPAY', 1);
        })
        ->submit();
});

test('MPG 啟用 LINE Pay', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods([
            'linePay' => [
                'enabled' => true,
                'image_url' => 'http://example.com/your-image-url',
            ],
        ])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('LINEPAY', 1);
            expect($tradeInfo)->toHaveKey('ImageUrl', 'http://example.com/your-image-url');
        })
        ->submit();
});

test('MPG 啟用 玉山 Wallet', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['esunWallet' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('ESUNWALLET', 1);
        })
        ->submit();
});

test('MPG 啟用台灣 Pay', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['taiwanPay' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('TAIWANPAY', 1);
        })
        ->submit();
});

test('MPG 啟用簡單付電子錢包', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['ezPay' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPAY', 1);
        })
        ->submit();
});

test('MPG 啟用簡單付微信支付', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['ezpWeChat' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPWECHAT', 1);
        })
        ->submit();
});

test('MPG 啟用簡單付支付寶', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['ezpAlipay' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPALIPAY', 1);
        })
        ->submit();
});

test('MPG 啟用超商代碼繳費支付', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['CVS' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CVS', 1);
        })
        ->submit();
});

test('MPG 啟用條碼繳費支付', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withPaymentMethods(['barcode' => true])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BARCODE', 1);
        })
        ->submit();
});

test('MPG 啟用超商取貨付款', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLogisticsPayment(CVSCOM::PAY)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CVSCOM', 2);
        })
        ->submit();
});

test('MPG 啟用 B2B 超商大宗寄倉', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLogisticsType(LgsType::B2C)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('LgsType', 'B2C');
        })
        ->submit();
});
