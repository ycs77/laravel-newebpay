<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\Payment\PaymentResult;

use function Pest\Laravel\partialMock;

test('可以成功呼叫 MPG 金流基本功能', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->expects('encryptByAES')->andReturn('encrypted_trade_data');
    $crypto->expects('hashBySHA')->andReturn('encrypted_sha_data');

    $response = NewebPay::payment()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->send();

    expect($response)->toBeInstanceOf(Response::class)
        ->content()->toContain('action="https://ccore.newebpay.com/MPG/mpg_gateway"')
        ->content()->toContain('name="MerchantID" value="TestMerchantID1234"')
        ->content()->toContain('name="TradeInfo" value="encrypted_trade_data"')
        ->content()->toContain('name="TradeSha" value="encrypted_sha_data"');
});

test('可以成功呼叫 MPG 金流的更多功能', function () {
    $expectedTradeData = [
        'MerchantID' => 'TestMerchantID1234',
        'RespondType' => 'JSON',
        'TimeStamp' => Carbon::now()->timestamp,
        'Version' => '2.0',
        'LangType' => 'en',
        'MerchantOrderNo' => 'Order001',
        'Amt' => 1050,
        'ItemDesc' => '測試商品',
        'TradeLimit' => 900,
        'ExpireDate' => '20200115',
        'ReturnURL' => 'https://example.com/return',
        'NotifyURL' => 'https://example.com/notify',
        'CustomerURL' => 'https://example.com/customer',
        'ClientBackURL' => 'https://example.com/back',
        'Email' => 'customer@example.com',
        'emailModify' => 0,
        'OrderComment' => '這是訂單備註',
        'TokenTerm' => 'John Doe',
        'CVSCom' => 3,
        'LgsType' => 'C2C',
        'CREDIT' => 1,
        'CreditRed' => 1,
        'VACC' => 1,
        'ANDROIDPAY' => 1,
        'SAMSUNGPAY' => 1,
        'LINEPAY' => 1,
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->expects('encryptByAES')->andReturn('encrypted_trade_data');
    $crypto->expects('hashBySHA')->andReturn('encrypted_sha_data');

    $response = NewebPay::payment()
        ->withLanguage(LangType::EN)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withTradeLimit(900)
        ->withExpireDays(14)
        ->withReturnUrl('https://example.com/return')
        ->withNotifyUrl('https://example.com/notify')
        ->withCustomerUrl('https://example.com/customer')
        ->withClientBackUrl('https://example.com/back')
        ->withEmail('customer@example.com')
        ->withEmailModify(false)
        ->withOrderComment('這是訂單備註')
        ->withPaymentMethods([
            'credit' => [
                'enabled' => true,
                'red' => true,
                'inst' => CreditInst::NONE,
            ],
            'VACC' => true,
            'googlePay' => true,
            'samsungPay' => true,
            'linePay' => true,
        ])
        ->withCreditRemember('John Doe')
        ->withLogisticsPayment(CVSCOM::NOT_PAY_AND_PAY)
        ->withLogisticsType(LgsType::C2C)
        ->onPreparedOptions(function (Options $options) use ($expectedTradeData) {
            expect($options->toArray()['TradeInfo'])->toBe($expectedTradeData);
        })
        ->send();

    expect($response)->toBeInstanceOf(Response::class);
});

test('可以解析 MPG 金流回傳資料', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->expects('decryptByAES')->andReturn([
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
    ]);

    $result = NewebPay::result(Request::create('/callback', 'POST', [
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => 'encrypted_trade_data',
        'TradeSha' => 'encrypted_sha_data',
        'Version' => '2.0',
        'EncryptType' => 1,
    ]));

    expect($result)->toBeInstanceOf(PaymentResult::class)
        ->and($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('授權成功')
        ->and($result->merchantId())->toBe('TestMerchantID1234')
        ->and($result->amount())->toBe(120)
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->orderNo())->toBe('1686759318')
        ->and($result->paymentType())->toBe(PaymentType::CREDIT)
        ->and($result->payTime()?->format('Y-m-d H:i:s'))->toBe('2023-01-01 00:00:00')
        ->and($result->ip())->toBe('127.0.0.1')
        ->and($result->escrowBank())->toBe('HNCB');
});
