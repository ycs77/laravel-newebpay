<?php

use Ycs77\LaravelRecoverSession\UserSource;
use Ycs77\NewebPay\NewebPayMPG;
use Ycs77\NewebPay\Senders\SyncSender;

test('NewebPay MPG can be get url', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getUrl())->toBe('https://ccore.newebpay.com/MPG/mpg_gateway');
});

test('NewebPay MPG sender is sync', function () {
    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    expect($newebpay->getSender())->toBeInstanceOf(SyncSender::class);
});

test('NewebPay MPG can be get request data', function () {
    setTestNow();

    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    $requestData = $newebpay->getRequestData();

    expect($requestData['MerchantID'])->toBe('TestMerchantID1234');
    expect($requestData['TradeInfo'])->toBe('59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367de483b0db0a497185e15c10cbf432e395b6764428d15b7364990f13e5d55977ac8363ac7d915caf60e705c980bb91b1288a16357a4c585e89516021d3168b4839e3310a7a00fee724572e94e4c74f17b75348f9841ca89cef5719f771da04eb70d100d024af70af4ebab4cbf8021594155eb68a629d63d159f1d7d9176dfa6ecea1e036cd55e0d6ee385304439ee5701be368453608e09ff4615c01f0d3e8d1b6a32be4dab6e723dda35f5d59ed83386ba651140ef7bb19ec12450eda6830022d4d040cd30ec35103e0c4fa7c26b3ee1765a90d350f94baca92520dcce50c38c');
    expect($requestData['TradeSha'])->toBe('7A59C97CB92020E53A079CC773DF6D072127A62A160E3DD65824F9CB43CAFCB4');
    expect($requestData['Version'])->toBe('2.0');
});

test('NewebPay MPG can be submit', function () {
    setTestNow();

    $newebpay = new NewebPayMPG(app('config'), app('session.store'), app(UserSource::class));

    $result = $newebpay
        ->setOrder('TestNo123456', 100, '測試商品', 'test@email.com')
        ->submit();

    expect($result)->toBe('<form id="order-form" method="post" action="https://ccore.newebpay.com/MPG/mpg_gateway"><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="59baba7fcc0fdce1f08910990735fce0f1531156700405540589c4e8cee8329d37876b24661b165bdb0d568f039510aa8c554894a3e52c63538e4d6c5b1d97ba1bd67ed7c219136b745fba38fbff2e5e133c68562c4f95349b62e9692107cafda1adc6a6debfee3d21c43ef39f8b86119f6c600632619f3d5386a2eb2d3d3367de483b0db0a497185e15c10cbf432e395b6764428d15b7364990f13e5d55977ac8363ac7d915caf60e705c980bb91b1288a16357a4c585e89516021d3168b4839e3310a7a00fee724572e94e4c74f17b75348f9841ca89cef5719f771da04eb70d100d024af70af4ebab4cbf8021594155eb68a629d63d159f1d7d9176dfa6ecea1e036cd55e0d6ee385304439ee5701be368453608e09ff4615c01f0d3e8d1b6a32be4dab6e723dda35f5d59ed83386ba651140ef7bb19ec12450eda6830022d4d040cd30ec35103e0c4fa7c26b3ee193f8373ba81a005867299543eb3bd74299070bd694355b66f1cce0982a947cc8e2893f0059676ee520bb0b13f3dff2cea806cdbf63e6507e6096c9737379348479b61f6ca6f26add55b456d7dafca116e02287177ed35f66e13b88073b73f1ad4a3dc0b0ac7c8ec783df0ec39dd14219bdb1844e1317e899fbba12ccc73dc5e4ca0c6314b670d26d970d9938c3e20dd5"><input type="hidden" name="TradeSha" value="B0F207BEF823D721AC76ACF109AA52C99BBD332CCA7DABF2612644EAB177EC35"><input type="hidden" name="Version" value="2.0"></form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>');
});
