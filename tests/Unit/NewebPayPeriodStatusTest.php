<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\NewebPayPeriodStatus;
use Ycs77\NewebPay\Senders\BackgroundSender;

test('period status can be get url', function () {
    $newebpay = new NewebPayPeriodStatus(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/period/AlterStatus');
});

test('period status sender is background', function () {
    $newebpay = new NewebPayPeriodStatus(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(BackgroundSender::class);
});

test('period status default post data', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodStatus(app('config'), app('session.store'));

    $postData = $newebpay
        ->alterStatus('TestNo123456', 'P230000000000AAA6AA', PeriodStatus::SUSPEND)
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.0',
        'RespondType' => 'JSON',
        'MerOrderNo' => 'TestNo123456',
        'PeriodNo' => 'P230000000000AAA6AA',
        'AlterType' => 'suspend',
    ]);
});

test('period status can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodStatus(app('config'), app('session.store'));

    $requestData = $newebpay
        ->alterStatus('TestNo123456', 'P230000000000AAA6AA', PeriodStatus::SUSPEND)
        ->requestData();

    expect($requestData['MerchantID_'])->toBe('TestMerchantID1234');
    expect($requestData['PostData_'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f3f0bcd99f286b2bf5de5c492d9a35effa2f9bfa4444860e8cdca7356a5e80ef97f14ba20cb7405e83d771fbbc7db09d0ba01100b47d45e2f233e134e7230ebf49f1764c1656e4b59e7faeba9df82c04d09ee66edf3981ffe75f040f3320f82fbbd1bec0c89a22e2fcf4060766c1ca2b3');
});

test('period status can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayPeriodStatus(app('config'), app('session.store'));

    $result = $newebpay
        ->alterStatus('TestNo123456', 'P230000000000AAA6AA', PeriodStatus::SUSPEND)
        ->setMockHttp(new Response(200, [], '{"period":"e24037a76a44cc9e050a40e04faa6c384ec4571da475a5634721f2d69688df22ecc41d12a83f4d9f139c488cef18d6ae4ec5bd8dab72b34b775eec26d7b19ebb"}'))
        ->submit();

    expect($result->data())->toBe([
        'Status' => 'SUCCESS',
        'Message' => 'Test message.',
        'Result' => [],
    ]);
});
