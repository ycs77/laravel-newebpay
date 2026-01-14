<?php

use Carbon\Carbon;
use Illuminate\Http\Response;
use Ycs77\NewebPay\Builders\MPG\MPGBuilder;
use Ycs77\NewebPay\Contracts\FormRedirectTransporter;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $this->response = new Response('<div>redirect form</div>');

    $this->factory = app(Factory::class);

    $this->crypto = mock(Crypto::class);
    $this->crypto->expects('setHashKey');
    $this->crypto->expects('setHashIv');
    $this->crypto->expects('encryptByAES')->andReturn('encrypted_data');
    $this->crypto->expects('hashBySHA')->andReturn('encrypted_data');

    $this->httpTransporter = mock(HttpTransporter::class);

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

    (new MPGBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->setFormRedirectTransporter($this->formRedirectTransporter)
        ->withOrder('Order001')
        ->withAmount(1050)
        ->withItemDescription('測試商品')
        ->withEmail('customer@example.com')
        ->onPreparedOptions(function (Options $options) use ($expectedTradeInfoData) {
            expect($options->toArray()['TradeInfo'])->toBe($expectedTradeInfoData);
        })
        ->submit();
});
