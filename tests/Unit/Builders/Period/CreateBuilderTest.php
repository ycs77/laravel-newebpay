<?php

use Carbon\Carbon;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\Period\CreateBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\PeriodStartType;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Url\UrlFormatter;

beforeEach(function () {
    $this->factory = mock(Factory::class);
    $this->factory->allows('baseUrl')->andReturn('https://example.com');

    $this->config = [
        'merchant_id' => 'TestMerchantID1234',
        'hash_key' => 'TestHashKey123456789',
        'hash_iv' => '17ef14e533ed1c18',
        'lang' => LangType::ZH_TW,
        'period' => [
            'return_url' => null,
            'notify_url' => null,
            'back_url' => null,
        ],
        'timeout' => 30,
    ];

    $this->crypto = mock(Crypto::class);
    $this->crypto->allows('setHashKey');
    $this->crypto->allows('setHashIv');
    $this->crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $this->httpTransporter = mock(HttpTransporter::class);

    $this->formRedirectTransporter = mock(FormRedirectTransporter::class);
    $this->formRedirectTransporter->allows('send')->andReturn(new Response);

    $this->urlFormatter = mock(UrlFormatter::class);
    $this->urlFormatter->allows('formatCallbackUrl')->andReturnUsing(fn (string $url) => 'http://localhost'.$url);
    $this->urlFormatter->allows('withSessionIdKey')->andReturnUsing(fn (string $url) => $url);
});

test('可以成功建立信用卡定期定額委託功能', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以每隔 40 天授權一次信用卡定期定額委託', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '40',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(40)
        ->times(3)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以每週日授權一次信用卡定期定額委託', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'W',
            'PeriodPoint' => '7',
            'PeriodTimes' => 1,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->weekly(7)
        ->times(1)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以每月 20 日授權一次信用卡定期定額委託', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'M',
            'PeriodPoint' => '20',
            'PeriodTimes' => 1,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->monthly(20)
        ->times(1)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以每年 3 月 4 日授權一次信用卡定期定額委託', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'Y',
            'PeriodPoint' => '0304',
            'PeriodTimes' => 1,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->yearly(3, 4)
        ->times(1)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以每月 4 日授權信用卡定期定額委託，共授權 6 次，為期 6 個月', function () {
    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->monthly(4)
        ->times(6)
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定信用卡定期定額委託授權方式', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 1,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->startWith(PeriodStartType::TEN_DOLLARS_NOW)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定立即執行十元授權', function () {
    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->startWithTenDollarAuth()
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定立即執行委託金額授權', function () {
    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->startWithImmediateAuth()
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以關閉付款人信箱修改功能', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
            'EmailModify' => 0,
            'PaymentInfo' => 'N',
            'OrderInfo' => 'N',
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->disableEmailModify()
        ->disablePaymentInfo()
        ->disableOrderInfo()
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定語系', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'LangType' => 'en',
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->withLang(LangType::EN)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以啟用銀聯卡', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
            'UNIONPAY' => 1,
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->enableUnionPay()
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定委託備註', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 2,
            'PeriodMemo' => '這是委託備註',
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->memo('這是委託備註')
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以設定不檢查信用卡資訊，也不執行授權', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.5',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'ProdDesc' => '測試商品',
            'PeriodAmt' => 1050,
            'PayerEmail' => 'customer@example.com',
            'PeriodType' => 'D',
            'PeriodPoint' => '2',
            'PeriodTimes' => 3,
            'PeriodStartType' => 3,
            'PeriodFirstdate' => '2023/03/01',
        ],
    ];

    $response = (new CreateBuilder($this->factory, $this->crypto, $this->httpTransporter, $this->urlFormatter, $this->config))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->everyFewDays(2)
        ->times(3)
        ->startWithoutAuth()
        ->firstChargeAt(2023, 3, 1)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});
