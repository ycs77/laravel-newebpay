<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayQuery;
use Ycs77\NewebPay\Senders\AsyncSender;

test('NewebPay query can be get url', function () {
    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/API/QueryTradeInfo');
});

test('NewebPay query sender is sync', function () {
    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(AsyncSender::class);
});

test('NewebPay query can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    $requestData = $newebpay
        ->setQuery('TestNo123456', 100)
        ->getRequestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['Version'])->toBe('2.0');
    expect($requestData['RespondType'])->toBe('JSON');
    expect($requestData['CheckValue'])->toBe('A314C865681049301D80A33318E5043B51425EAC58736E9ACF4FAC5854ABD59F');
    expect($requestData['TimeStamp'])->toBe(1577836800);
    expect($requestData['MerchantOrderNo'])->toBe('TestNo123456');
    expect($requestData['Amt'])->toBe(100);
});

test('NewebPay query can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayQuery(app('config'), app('session.store'));

    $result = $newebpay
        ->setQuery('TestNo123456', 100)
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
