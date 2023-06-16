<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCancel;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('cancel can be get url', function () {
    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/CreditCard/Cancel');
});

test('cancel sender is background', function () {
    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('cancel default post data', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    $postData = $newebpay->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
    ]);
});

test('cancel order post data with "MerchantOrderNo"', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    $postData = $newebpay
        ->cancelOrder('TestNo123456', 100, 'order')
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'MerchantOrderNo' => 'TestNo123456',
        'IndexType' => 1,
        'Amt' => 100,
    ]);
});

test('cancel order post data with "TradeNo"', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    $postData = $newebpay
        ->cancelOrder('TestNo123456', 100, 'trade')
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'TradeNo' => 'TestNo123456',
        'IndexType' => 2,
        'Amt' => 100,
    ]);
});

test('cancel can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    $requestData = $newebpay->requestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f3f0bcd99f286b2bf5de5c492d9a35effa2f9bfa4444860e8cdca7356a5e80ef9c445f92e77a2229e1f04d1a7e9386120');
});

test('cancel can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayCancel(app('config'), app('session.store'));

    $result = $newebpay
        ->cancelOrder('TestNo123456', 100, 'order')
        ->setMockHttp(new Response(200, [], '{"Status":"SUCCESS","Message":"Test message.","Result":[]}'))
        ->submit();

    expect($result->data())->toBe([
        'Status' => 'SUCCESS',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
