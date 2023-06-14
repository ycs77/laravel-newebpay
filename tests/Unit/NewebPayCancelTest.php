<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\NewebPayCancel;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('NewebPay cancel can be get url', function () {
    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Cancel');
});

test('NewebPay cancel sender is background', function () {
    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('NewebPay cancel default post data', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    $postData = $newebpay->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'NotifyURL' => 'http://localhost',
    ]);
});

test('NewebPay cancel order post data with "MerchantOrderNo"', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    $postData = $newebpay
        ->cancelOrder('TestNo123456', 100, 'order')
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'NotifyURL' => 'http://localhost',
        'MerchantOrderNo' => 'TestNo123456',
        'IndexType' => 1,
        'Amt' => 100,
    ]);
});

test('NewebPay cancel order post data with "TradeNo"', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    $postData = $newebpay
        ->cancelOrder('TestNo123456', 100, 'trade')
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'NotifyURL' => 'http://localhost',
        'TradeNo' => 'TestNo123456',
        'IndexType' => 2,
        'Amt' => 100,
    ]);
});

test('NewebPay cancel can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    $requestData = $newebpay->requestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f3f0bcd99f286b2bf5de5c492d9a35effa2f9bfa4444860e8cdca7356a5e80ef9bf12a2bfc495dc80815c851e7107f0b7714c4624aa129262e4bbb0a5cb02e3371ea1d7b5525e971be952b1d1a6368ab6');
});

test('NewebPay cancel can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'), app(UserSource::class));

    $result = $newebpay
        ->cancelOrder('TestNo123456', 100, 'order')
        ->setMockHttp(new Response(200, [], '{"Status":"Code001","Message":"Test message.","Result":[]}'))
        ->submit();

    expect($result)->toBe([
        'Status' => 'Code001',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
