<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayPeriodAmt;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('period amt can be get url', function () {
    $newebpay = new NewebPayPeriodAmt(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/period/AlterAmt');
});

test('period amt sender is background', function () {
    $newebpay = new NewebPayPeriodAmt(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('period amt default post data', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodAmt(app('config'), app('session.store'));

    $postData = $newebpay
        ->alter('TestNo123456', 'P230000000000AAA6AA', 120)
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.1',
        'RespondType' => 'JSON',
        'MerOrderNo' => 'TestNo123456',
        'PeriodNo' => 'P230000000000AAA6AA',
        'AlterAmt' => 120,
    ]);
});

test('period amt can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodAmt(app('config'), app('session.store'));

    $requestData = $newebpay
        ->alter('TestNo123456', 'P230000000000AAA6AA', 120)
        ->requestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421fe388e2693f8650b343563c222b888351c8621daeed9525d76478c3dc00f4054f3f488c35cbd5f3594df96fddbbf70f267c9eb4150a35db98fe01b2c34618fd15e532c402e787f8951e15edda8e65ebc1d56a848ac7d36c7c7b5ebfc7e8e7f90e6bf21a035cd0a972405293fb27c8d303');
});

test('period amt can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodAmt(app('config'), app('session.store'));

    $result = $newebpay
        ->alter('TestNo123456', 'P230000000000AAA6AA', 120)
        ->setMockHttp(new Response(200, [], '{"period":"e24037a76a44cc9e050a40e04faa6c384ec4571da475a5634721f2d69688df22ecc41d12a83f4d9f139c488cef18d6ae4ec5bd8dab72b34b775eec26d7b19ebb"}'))
        ->submit();

    expect($result->data())->toBe([
        'Status' => 'SUCCESS',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
