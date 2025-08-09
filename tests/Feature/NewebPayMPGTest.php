<?php

use Illuminate\Http\Response;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Facades\NewebPay;

use function Pest\Laravel\partialMock;

test('可以成功呼叫 MPG 金流功能', function () {
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
