<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\NewebPayClose;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('NewebPay close can be get url', function () {
    $newebpay = new NewebPayClose(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Close');
});

test('NewebPay close sender is sync', function () {
    $newebpay = new NewebPayClose(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('NewebPay close can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'), app(UserSource::class));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f9c4dc994fdb1a0acb2c79d95ee134b224e2d30fda9b31515d49d15c31b82cc10fdf973050257254be056f55b71c290876bf945b919aa697d0834872ada92e3fe58d1b25c1e6ae75cc821cedac1bc11dd');
});

test('NewebPay close can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayClose(app('config'), app('session.store'), app(UserSource::class));

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
