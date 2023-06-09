<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCreditCard;
use Ycs77\NewebPay\Senders\AsyncSender;

test('NewebPay credit card can be get url', function () {
    $newebpay = new NewebPayCreditCard(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard');
});

test('NewebPay credit card sender is sync', function () {
    $newebpay = new NewebPayCreditCard(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(AsyncSender::class);
});

test('NewebPay credit card can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayCreditCard(app('config'), app('session.store'));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f9c4dc994fdb1a0acb2c79d95ee134b224e2d30fda9b31515d49d15c31b82cc104d3dff0ead8c63028996df9e8f164e74');
    expect($requestData['Pos_'])->toBe('JSON');
});

test('NewebPay credit card can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayCreditCard(app('config'), app('session.store'));

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
