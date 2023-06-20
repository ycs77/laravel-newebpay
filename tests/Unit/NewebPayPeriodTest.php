<?php

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayPeriod;
use Ycs77\NewebPay\Senders\FrontendSender;

test('period can be get url', function () {
    $newebpay = new NewebPayPeriod(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/period');
});

test('period sender is frontend', function () {
    $newebpay = new NewebPayPeriod(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(FrontendSender::class);
});

test('period can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayPeriod(app('config'), app('session.store'));

    $postData = $newebpay
        ->periodOrder('TestNo123456', 100, '測試商品', 'test@email.com')
        ->postData();

    expect($postData)->toBe([
        'TimeStamp' => 1577836800,
        'Version' => '1.5',
        'RespondType' => 'JSON',
        'EmailModify' => 0,
        'PaymentInfo' => 'N',
        'OrderInfo' => 'N',
        'PeriodStartType' => 2,
        'MerOrderNo' => 'TestNo123456',
        'PeriodAmt' => 100,
        'ProdDesc' => '測試商品',
        'PayerEmail' => 'test@email.com',
    ]);
});

test('period can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayPeriod(app('config'), app('session.store'));

    $result = $newebpay
        ->periodOrder('TestNo123456', 100, '測試商品', 'test@email.com')
        ->setMockHttp(new Response(200, [], '{"Status":"SUCCESS","Message":"Test message.","Result":[]}'))
        ->submit();

    expect($result)->toBe('<!DOCTYPE html><html><head><meta charset="utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1"></head><body><form id="order-form" method="post" action="https://ccore.newebpay.com/MPG/period"><input type="hidden" name="MerchantID_" value="TestMerchantID1234"><input type="hidden" name="PostData_" value="e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b689c1d86ee7c4686f31d9862505437de695c39dac952c7bad47464e3b83e7ef4806ba02ef5c276b1603ead1a396eb4f472777a04f7d2eb5d7ea3ea5d6ceccbabcdfb8bbde41331aae7e912324168f530324a56e7503cc46ad5ea6ab30d8f479e0827a586bfb6e0c7bacb779d3317d26c43afd5e1db73580ba53dd819f79b67c7f692fe29d4cb8b884a8ff91e92d1caf5d28cf19f69a59f9b790cb4da009104cc047f70f320b0c1fb02d5dc590503e51f5"></form><script>document.getElementById("order-form").submit();</script></body></html>');
});
