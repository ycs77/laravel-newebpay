<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCreditCard;
use Ycs77\NewebPay\Sender\Async;

test('NewebPay credit card can be get url', function () {
    $newebpay = new NewebPayCreditCard(app('config'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard');
});

test('NewebPay credit card sender is sync', function () {
    $newebpay = new NewebPayCreditCard(app('config'));

    expect($newebpay->getSender())->toBeInstanceOf(Async::class);
});

test('NewebPay credit card can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayCreditCard(app('config'));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b6b4bc2294c8153642aa02cc63afaa4b16');
    expect($requestData['Pos_'])->toBe('JSON');
});

test('NewebPay credit card can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayCreditCard(app('config'));

    $result = $newebpay
        ->firstTrade([
            'no' => 'TestNo123456',
            'amt' => 100,
            'desc' => '測試商品',
            'email' => 'test@email.com',
            'cardNo' => '0000-0000-0000-0000',
            'exp' => '',
            'cvc' => '',
            'tokenTerm' => '',
        ])
        ->setMockHttp([
            new Response(200, [], '{"Status":"Code001","Message":"Test message.","Result":[]}'),
        ])
        ->submit();

    expect($result)->toBe([
        'Status' => 'Code001',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
