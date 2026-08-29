<?php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Results\Trade\CustomerResult;

use function Pest\Laravel\partialMock;

test('MPG 金流 → 解析 ATM/超商條碼/超商代碼 取號資料', function () {
    $crypto = partialMock(Crypto::class);
    $crypto->allows('decryptByAES')->andReturn([
        'Status' => 'SUCCESS',
        'Message' => '條碼取號成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'Amt' => 120,
            'TradeNo' => '23061500000000000',
            'MerchantOrderNo' => '1686763446',
            'PaymentType' => 'BARCODE',
            'RespondType' => 'JSON',
            'ExpireDate' => '2023-01-01',
            'ExpireTime' => '23:59:59',
            'Barcode_1' => 'TEST1',
            'Barcode_2' => 'TEST2',
            'Barcode_3' => 'TEST3',
        ],
    ]);

    $result = NewebPay::customer(Request::create('/callback', 'POST', [
        'Status' => 'SUCCESS',
        'MerchantID' => 'TestMerchantID1234',
        'TradeInfo' => 'encrypted_trade_data',
        'TradeSha' => 'encrypted_sha_data',
        'Version' => '2.0',
        'EncryptType' => 1,
    ]));

    expect($result)->toBeInstanceOf(CustomerResult::class)
        ->and($result->status())->toBe('SUCCESS')
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->isFail())->toBeFalse()
        ->and($result->message())->toBe('條碼取號成功')
        ->and($result->merchantID())->toBe('TestMerchantID1234')
        ->and($result->amount())->toBe(120)
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->orderNo())->toBe('1686763446')
        ->and($result->paymentType())->toBe(PaymentType::BARCODE)
        ->and($result->expireTime()?->format('Y-m-d H:i:s'))->toBe('2023-01-01 23:59:59');
});
