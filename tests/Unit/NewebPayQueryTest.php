<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayQuery;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('query can be get url', function () {
    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/QueryTradeInfo');
});

test('query sender is background', function () {
    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('query can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    $requestData = $newebpay
        ->query('TestNo123456', 100)
        ->requestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['Version'])->toBe('1.3');
    expect($requestData['RespondType'])->toBe('JSON');
    expect($requestData['CheckValue'])->toBe('A314C865681049301D80A33318E5043B51425EAC58736E9ACF4FAC5854ABD59F');
    expect($requestData['TimeStamp'])->toBe(1577836800);
    expect($requestData['MerchantOrderNo'])->toBe('TestNo123456');
    expect($requestData['Amt'])->toBe(100);
});

test('query can be set gateway is "Composite"', function () {
    setTestNow();

    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    $requestData = $newebpay
        ->query('TestNo123456', 100)
        ->gateway('Composite')
        ->requestData();

    expect($requestData)->toHaveKey('Gateway', 'Composite');
});

test('query can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    $result = $newebpay
        ->query('TestNo123456', 100)
        ->setMockHttp(new Response(200, [], '{"Status":"SUCCESS","Message":"Test message.","Result":[]}'))
        ->submit();

    expect($result->data())->toBe([
        'Status' => 'SUCCESS',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
