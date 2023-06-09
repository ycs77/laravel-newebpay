<?php

use Ycs77\NewebPay\NewebPayMPG;
use Ycs77\NewebPay\Senders\SyncSender;

test('NewebPay MPG can be get url', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/mpg_gateway');
});

test('NewebPay MPG sender is sync', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    expect($newebpay->getSender())->toBeInstanceOf(SyncSender::class);
});

test('NewebPay MPG can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['TradeInfo'])->toBe('e88e33cc07d106bcba1c1bd02d5d421f9c4dc994fdb1a0acb2c79d95ee134b224e2d30fda9b31515d49d15c31b82cc10495fc4f238656c76fbff6a5aa6dcceb36c0939d566a605915b8e3871d45f6541c124f15c4428080f536195f8ee2384e3b4230652bc2b1f87a05ed1bf8e700d9d4fc4e9689b76828a0dd0e0353b7b6225980dd3a3fd6d5693e8c7750da5df54febfcfe11a83ef0eb41330a5c09ec2411da0bfa087bef859acad9f7f0c11288baf471cc65937a81774af13faa01027866e119f77d24c5764394196c979383798b76b36e6862507ea9f350e3ce1c2176837822b2afb236c0366a27229dadc859c662cf00a63edf291765ea99e26f63ae5b775195b328da28e2d9fffe7dd0ee555ab573ed00bbf5a9ce6ffd46175ec1351321c317642ea1f7f949791221a8f140dced15424a604314dbcd70a76c41502b55eb894c4a848ff9c320e96d280030f4dd462208a2d03128234fbbe62534c17b36c');
    expect($requestData['TradeSha'])->toBe('4287E10E3AAAE7343267AA5971F56E9F3C03576C14EA9DF243D2BF5D8187C35B');
    expect($requestData['Version'])->toBe('2.0');
});

test('NewebPay MPG can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayMPG(app('config'), app('session.store'));

    $result = $newebpay
        ->setOrder('TestNo123456', 100, '測試商品', 'test@email.com')
        ->submit();

    expect($result)->toBe('<form id="order-form" method="post" action="https://ccore.newebpay.com/MPG/mpg_gateway"><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="e88e33cc07d106bcba1c1bd02d5d421f9c4dc994fdb1a0acb2c79d95ee134b224e2d30fda9b31515d49d15c31b82cc10495fc4f238656c76fbff6a5aa6dcceb36c0939d566a605915b8e3871d45f6541c124f15c4428080f536195f8ee2384e3b4230652bc2b1f87a05ed1bf8e700d9d4fc4e9689b76828a0dd0e0353b7b6225980dd3a3fd6d5693e8c7750da5df54febfcfe11a83ef0eb41330a5c09ec2411da0bfa087bef859acad9f7f0c11288baf471cc65937a81774af13faa01027866e119f77d24c5764394196c979383798b76b36e6862507ea9f350e3ce1c2176837822b2afb236c0366a27229dadc859c662cf00a63edf291765ea99e26f63ae5b775195b328da28e2d9fffe7dd0ee555ab573ed00bbf5a9ce6ffd46175ec1351321c317642ea1f7f949791221a8f140dced15424a604314dbcd70a76c41502b55eb894c4a848ff9c320e96d280030f4dd431796851b3c7f44412b94b0e64672eac8e1f3d4b333c7c179aed36697ec7a2d2b2e9c97460c62246291e2df36514a8ee8ff061b4a51b9d2ce28b3e85d840eacdea3b1fe24b16c7220940ab5d9a141abc1ba6afc0d15340c45aee25044988008ca6797f8c1074b8ac35a7093457b0455b2a3530d3631388fa5a6a583da38cec93cba43cf615adf375fd5d3f8453d15058"><input type="hidden" name="TradeSha" value="08972A0B69A023CBDB6B57C537F9C810C477C9481A8CE31524B312724B67491D"><input type="hidden" name="Version" value="2.0"></form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>');
});
