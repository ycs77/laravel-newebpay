<?php

use Carbon\Carbon;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Trade\MPGBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Url\PrependAppUrl;
use Ycs77\NewebPay\Url\WithSessionIdKey;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $this->factory = mock(Factory::class);
    $this->factory->allows('baseUrl')->andReturn('https://example.com');

    $this->config = [
        'merchant_id' => 'TestMerchantID1234',
        'hash_key' => 'TestHashKey123456789',
        'hash_iv' => '17ef14e533ed1c18',
        'lang' => LangType::ZH_TW,
        'timeout' => 30,
    ];

    $this->crypto = mock(Crypto::class);
    $this->crypto->allows('setHashKey');
    $this->crypto->allows('setHashIv');
    $this->crypto->allows('encryptByAES')->andReturn('encrypted_data');
    $this->crypto->allows('hashBySHA')->andReturn('encrypted_data');

    $this->httpTransporter = mock(HttpTransporter::class);

    $this->response = new Response('<div>redirect form</div>');

    $this->formRedirectTransporter = mock(FormRedirectTransporter::class);
    $this->formRedirectTransporter->allows('send')->andReturn($this->response);

    $this->withSessionIdKey = mock(WithSessionIdKey::class);
    $this->withSessionIdKey->allows('handle')->andReturnUsing(fn (string $url) => $url);

    $this->prependAppUrl = mock(PrependAppUrl::class);
    $this->prependAppUrl->allows('handle')->andReturnUsing(fn (string $url) => $url);
});

test('MPGBuilder → 基本設定（預設不啟用任何付款方式）', function () {
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
    ];

    $response = (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->withReturnUrl('http://localhost/pay/callback')
        ->withNotifyUrl('http://localhost/pay/notify')
        ->withCustomerUrl('http://localhost/pay/customer')
        ->onPreparedOptions(function (Options $options) use ($expectedTradeInfoData) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            // 未顯式設定任何付款方式時，不應出現任何付款方式參數
            expect($tradeInfo)->toBe($expectedTradeInfoData);
            expect($tradeInfo)->not->toHaveKey('CREDIT');
            expect($tradeInfo)->not->toHaveKey('WEBATM');
            expect($tradeInfo)->not->toHaveKey('VACC');
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('MPGBuilder → 啟用信用卡（withCredit）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->not->toHaveKey('CreditRed');
            expect($tradeInfo)->not->toHaveKey('InstFlag');
        })
        ->submit();
});

test('MPGBuilder → 信用卡啟用紅利交易（withCredit red）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit(red: true)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->toHaveKey('CreditRed', 1);
        })
        ->submit();
});

test('MPGBuilder → 信用卡啟用單個分期付款選項（withCredit inst）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit(inst: CreditInst::P3)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->toHaveKey('InstFlag', '3');
        })
        ->submit();
});

test('MPGBuilder → 信用卡啟用多個分期付款選項（withCredit inst array）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit(inst: [CreditInst::P3, CreditInst::P6, CreditInst::P12])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->toHaveKey('InstFlag', '3,6,12');
        })
        ->submit();
});

test('MPGBuilder → 信用卡紅利與分期同時設定（withCredit red + inst）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit(red: true, inst: [CreditInst::P3, CreditInst::P6])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CREDIT', 1);
            expect($tradeInfo)->toHaveKey('CreditRed', 1);
            expect($tradeInfo)->toHaveKey('InstFlag', '3,6');
        })
        ->submit();
});

test('MPGBuilder → 信用卡記憶卡號（withCreditRemember）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCredit()
        ->withCreditRemember('example_user')
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('TokenTerm', 'example_user');
            expect($tradeInfo)->toHaveKey('TokenTermDemand', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用 WebATM（withWebAtm）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withWebAtm()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('WEBATM', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用 ATM 轉帳（withAtmTransfer）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withAtmTransfer()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('VACC', 1);
        })
        ->submit();
});

test('MPGBuilder → 指定單個轉帳銀行（withBank）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withAtmTransfer()
        ->withBank(Bank::BOT)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BankType', 'BOT');
        })
        ->submit();
});

test('MPGBuilder → 指定多個轉帳銀行（withBank array）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withAtmTransfer()
        ->withBank([Bank::BOT, Bank::HNCB])
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BankType', 'BOT,HNCB');
        })
        ->submit();
});

test('MPGBuilder → 啟用國民旅遊卡（withNationalTravelCard）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withNationalTravelCard(NTCBLocate::HsinchuCity, '2020-01-01', '2020-01-01')
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('NTCB', 1);
            expect($tradeInfo)->toHaveKey('NTCBLocate', '005');
            expect($tradeInfo)->toHaveKey('NTCBStartDate', '2020-01-01');
            expect($tradeInfo)->toHaveKey('NTCBEndDate', '2020-01-01');
        })
        ->submit();
});

test('MPGBuilder → 啟用 Google Pay（withGooglePay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withGooglePay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('ANDROIDPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用 Samsung Pay（withSamsungPay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withSamsungPay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('SAMSUNGPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用 LINE Pay（withLinePay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLinePay(imageUrl: 'http://example.com/your-image-url')
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('LINEPAY', 1);
            expect($tradeInfo)->toHaveKey('ImageUrl', 'http://example.com/your-image-url');
        })
        ->submit();
});

test('MPGBuilder → 啟用 LINE Pay 不帶產品圖檔（withLinePay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLinePay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('LINEPAY', 1);
            expect($tradeInfo)->not->toHaveKey('ImageUrl');
        })
        ->submit();
});

test('MPGBuilder → 啟用銀聯卡（withUnionPay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withUnionPay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('UNIONPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用玉山 Wallet（withEsunWallet）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withEsunWallet()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('ESUNWALLET', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用台灣 Pay（withTaiwanPay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withTaiwanPay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('TAIWANPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用簡單付電子錢包（withEzPay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withEzPay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用簡單付微信支付（withEzPayWeChat）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withEzPayWeChat()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPWECHAT', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用簡單付支付寶（withEzPayAlipay）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withEzPayAlipay()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('EZPALIPAY', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用超商代碼繳費（withCvsCode）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withCvsCode()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CVS', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用條碼繳費（withBarcode）', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withBarcode()
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('BARCODE', 1);
        })
        ->submit();
});

test('MPGBuilder → 啟用超商取貨付款', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLogisticsPayment(CVSCOM::PAY)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('CVSCOM', 2);
        })
        ->submit();
});

test('MPGBuilder → 啟用 B2B 超商大宗寄倉', function () {
    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->withSessionIdKey, $this->prependAppUrl, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withLogisticsType(LgsType::B2C)
        ->onPreparedOptions(function (Options $options) {
            $tradeInfo = $options->toArray()['TradeInfo'];
            expect($tradeInfo)->toHaveKey('LgsType', 'B2C');
        })
        ->submit();
});
