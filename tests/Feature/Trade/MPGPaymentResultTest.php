<?php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Results\Trade\PaymentResult;

use function Pest\Laravel\partialMock;

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
