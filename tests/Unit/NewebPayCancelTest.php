<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCancel;
use Ycs77\NewebPay\Sender\Async;

test('NewebPay cancel can be get url', function () {
    $newebpay = new NewebPayCancel(app('config'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Cancel');
});

test('NewebPay cancel sender is sync', function () {
    $newebpay = new NewebPayCancel(app('config'));

    expect($newebpay->getSender())->toBeInstanceOf(Async::class);
});

test('NewebPay cancel can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f9c4dc994fdb1a0acb2c79d95ee134b224e2d30fda9b31515d49d15c31b82cc1060a7433f19224003d9f271bf9c56f9d2');
});

test('NewebPay cancel can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'));

    $result = $newebpay
        ->setCancelOrder('TestNo123456', 100, 'order')
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
