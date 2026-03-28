<?php

use Carbon\Carbon;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Options;

use function Pest\Laravel\partialMock;

test('可以成功呼叫 MPG 金流基本功能', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_trade_data');
    $crypto->allows('hashBySHA')->andReturn('encrypted_sha_data');

    $response = NewebPay::payment()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->submit();

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->content())->toContain('action="https://ccore.newebpay.com/MPG/mpg_gateway"')
        ->and($response->content())->toContain('action="https://ccore.newebpay.com/MPG/mpg_gateway"')
        ->and($response->content())->toContain('name="MerchantID" value="TestMerchantID1234"')
        ->and($response->content())->toContain('name="TradeInfo" value="encrypted_trade_data"')
        ->and($response->content())->toContain('name="TradeSha" value="encrypted_sha_data"');
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
        'EmailModify' => 0,
        'OrderComment' => '這是訂單備註',
        'TokenTerm' => 'John Doe',
        'TokenTermDemand' => 1,
        'CVSCOM' => 3,
        'LgsType' => 'C2C',
        'CREDIT' => 1,
        'CreditRed' => 1,
        'VACC' => 1,
        'ANDROIDPAY' => 1,
        'SAMSUNGPAY' => 1,
        'LINEPAY' => 1,
    ];

    $crypto = partialMock(Crypto::class);
    $crypto->allows('encryptByAES')->andReturn('encrypted_trade_data');
    $crypto->allows('hashBySHA')->andReturn('encrypted_sha_data');

    $response = NewebPay::payment()
        ->withLang(LangType::EN)
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
        ->disableEmailModify()
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
        ->submit();

    expect($response)->toBeInstanceOf(Response::class);
});
