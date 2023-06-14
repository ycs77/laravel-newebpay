<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayClose;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('NewebPay close can be get url', function () {
    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Close');
});

test('NewebPay close sender is background', function () {
    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('NewebPay close post data for request pay', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $newebpay
        ->closeOrder('TestNo123456', 100, 'order')
        ->pay();

    expect($newebpay->postData())->toHaveKey('MerchantOrderNo', 'TestNo123456');
    expect($newebpay->postData())->toHaveKey('IndexType', 1);
    expect($newebpay->postData())->toHaveKey('Amt', 100);
    expect($newebpay->postData())->toHaveKey('CloseType', 1);
    expect($newebpay->postData())->not->toHaveKey('Cancel');
});

test('NewebPay close post data for request refund', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $newebpay
        ->closeOrder('TestNo123456', 100, 'order')
        ->refund();

    expect($newebpay->postData())->toHaveKey('MerchantOrderNo', 'TestNo123456');
    expect($newebpay->postData())->toHaveKey('IndexType', 1);
    expect($newebpay->postData())->toHaveKey('Amt', 100);
    expect($newebpay->postData())->toHaveKey('CloseType', 2);
    expect($newebpay->postData())->not->toHaveKey('Cancel');
});

test('NewebPay close post data for cancel request pay', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $newebpay
        ->closeOrder('TestNo123456', 100, 'order')
        ->pay()
        ->cancel();

    expect($newebpay->postData())->toHaveKey('MerchantOrderNo', 'TestNo123456');
    expect($newebpay->postData())->toHaveKey('IndexType', 1);
    expect($newebpay->postData())->toHaveKey('Amt', 100);
    expect($newebpay->postData())->toHaveKey('CloseType', 1);
    expect($newebpay->postData())->toHaveKey('Cancel', 1);
});

test('NewebPay close post data for cancel request refund', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $newebpay
        ->closeOrder('TestNo123456', 100, 'order')
        ->refund()
        ->cancel();

    expect($newebpay->postData())->toHaveKey('MerchantOrderNo', 'TestNo123456');
    expect($newebpay->postData())->toHaveKey('IndexType', 1);
    expect($newebpay->postData())->toHaveKey('Amt', 100);
    expect($newebpay->postData())->toHaveKey('CloseType', 2);
    expect($newebpay->postData())->toHaveKey('Cancel', 1);
});

test('NewebPay close can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $requestData = $newebpay->requestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421fe388e2693f8650b343563c222b888351c8621daeed9525d76478c3dc00f4054f1f29d08ddacef23d54447502d4bea5d44ef78d32986b84fa283e2d98194249e5ee0be935b573ec85f5ea51b0f9c95a24');
});

test('NewebPay close can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'));

    $result = $newebpay
        ->closeOrder('TestNo123456', 100, 'order')
        ->setMockHttp(new Response(200, [], '{"Status":"Code001","Message":"Test message.","Result":[]}'))
        ->submit();

    expect($result)->toBe([
        'Status' => 'Code001',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
