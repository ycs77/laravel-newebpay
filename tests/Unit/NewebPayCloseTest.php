<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayClose;
use Ycs77\NewebPay\Sender\Async;

test('NewebPay close can be get url', function () {
    $newebpay = new NewebPayClose(app('config'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Close');
});

test('NewebPay close sender is sync', function () {
    $newebpay = new NewebPayClose(app('config'));

    expect($newebpay->getSender())->toBeInstanceOf(Async::class);
});

test('NewebPay close can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b6c2ad3875eb127ca33809ddd77de94550');
});

test('NewebPay close can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'));

    $result = $newebpay
        ->setCloseOrder('TestNo123456', 100, 'order')
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
