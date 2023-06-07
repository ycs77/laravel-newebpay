# Laravel NewebPay - 藍新金流

> Fork from [treerful/laravel-newebpay](https://bitbucket.org/pickone/laravel-newebpay)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Style CI Build Status][ico-style-ci]][link-style-ci]
[![Total Downloads][ico-downloads]][link-downloads]
<!-- [![CI Build Status][ico-ci]][link-ci] -->

Laravel NewebPay 為針對 Laravel 所寫的金流套件，主要實作藍新金流（原智付通）功能。

主要實作項目：

* NewebPay MPG - 多功能收款
* NewebPay Cancel - 信用卡取消授權
* NewebPay Close - 信用卡請退款

## 安裝

```
composer require ycs77/laravel-newebpay
```

### 註冊套件

> Laravel 5.5 以上會自動註冊套件，可以跳過此步驟

在 `config/app.php` 註冊套件和增加別名：

```php
    'providers' => [
        ...

        /*
         * Package Service Providers...
         */
        Ycs77\NewebPay\NewebPayServiceProvider::class,
    ],

    'aliases' => [
        ...
        'NewebPay' => Ycs77\NewebPay\Facades\NewebPay::class,
    ]
```

### 發布設置檔案

```
php artisan vendor:publish --provider="Ycs77\NewebPay\NewebPayServiceProvider"
```

## 使用

首先先到藍新金流的網站上註冊帳號 (測試時需註冊測試帳號)，和建立商店。然後在「商店資料設定」中啟用需要使用的金流功能 (測試時可以盡量全部啟用)，並複製商店串接 API 的商店代號、`HashKey` 和 `HashIV`。

設定 `.env` 檔，更多設定需開啟 `config/newebpay.php` 修改：

```
NEWEBPAY_STORE_ID=...        # 貼上 商店代號 (Ex: MS3311...)
NEWEBPAY_STORE_HASH_KEY=...  # 貼上 HashKey
NEWEBPAY_STORE_HASH_IV=...   # 貼上 HashIV
NEWEBPAY_DEBUG=true    # debug 模式

NEWEBPAY_RETURN_URL=...      # 付款完成後，前端重導向回來的網址 (Ex: /pay/callback)
NEWEBPAY_NOTIFY_URL=...      # 付款完成後，後端自動響應的網址   (Ex: /pay/notify)
NEWEBPAY_CLIENT_BACK_URL=... # 取消付款時，返回的網址          (Ex: /pay/cancel)
```

首先先建立一個頁面，和一個「付款」按鈕：

```php
// 路由
Route::get('/pay', function () {
    return view('pay');
});
```

*resources/views/pay.blade.php*
```html
<form action="/pay" method="POST">
    @csrf
    <button>付款</button>
</form>
```

Inertia.js 可以參考以下：

```php
// 路由
Route::get('/pay', function () {
    return Inertia::render('Pay', ['csrf_token' => csrf_token()]);
});
```

*resources/js/pages/Pay.vue*
```vue
<template>
  <form action="/pay" method="POST">
    <input type="hidden" name="_token" :value="csrf_token">
    <button>付款</button>
  </form>
</template>

<script>
export default {
  props: {
    csrf_token: String
  }
}
</script>
```

然後建立送出付款的路由：

```php
// 路由
Route::post('/pay', 'PaymentController@payment');

// PaymentController.php

use Ycs77\NewebPay\Facades\NewebPay;

function payment()
{
    $no = '0001';                // 訂單編號
    $amt = 120;                  // 交易金額
    $desc = '我的商品';           // 商品名稱
    $email = 'test@example.com'; // 付款人信箱

    return NewebPay::payment($no, $amt, $desc, $email)->submit();
}
```

然後是回傳的路由，如果是信用卡之類的付款方式，可以付款後直接跳轉回本網站的，可以只設定 callback。如果是 ATM 的付款方式，需要透過幕後回傳的，可以只設定 notify。

> 記得要在 `.env` 裡設定網址。

```php
// 路由
Route::post('/pay/callback', 'PaymentController@callback');
Route::post('/pay/notify', 'PaymentController@notify');

// PaymentController.php

use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

function callback(Request $request)
{
    $data = NewebPay::decodeFromRequest();
    dd($data);
    // 儲存資料 和 重導向...
}

function notify(Request $request)
{
    $data = NewebPay::decodeFromRequest();
    dd($data);
    // 儲存資料 和 發送付款成功通知...
}
```

然後要把這些路徑排除 CSRF 檢查：

*app/Http/Middleware/VerifyCsrfToken.php*
```php
protected $except = [
    '/pay/callback',
    '/pay/notify',
];
```

### NewebPay MPG - 多功能支付

```php
use Ycs77\NewebPay\Facades\NewebPay;

function order()
{
    return NewebPay::payment(
        no, // 訂單編號
        amt, // 交易金額
        desc, // 商品名稱
        email // 付款人信箱
    )->submit();
}
```

基本上一般交易可直接在 `config/newebpay.php` 做設定，裡面有詳細的解說，但若遇到特殊情況，可依據個別交易做個別 function 設定。

```php
use Ycs77\NewebPay\Facades\NewebPay;

return NewebPay::payment(
    no, // 訂單編號
    amt, // 交易金額
    desc, // 商品名稱
    email // 付款人信箱
)
    ->setRespondType() // 回傳格式
    ->setLangType() // 語言設定
    ->setTradeLimit() // 交易秒數限制
    ->setExpireDate() // 交易截止日
    ->setReturnURL() // 由藍新回傳後前景畫面要接收資料顯示的網址
    ->setNotifyURL() // 由藍新回傳後背景處理資料的接收網址
    ->setCutomerURL() // 商店取號網址
    ->setClientBackURL() // 付款取消後返回的網址
    ->setEmailModify() // 是否開放 email 修改
    ->setLoginType() // 是否需要登入藍新金流會員
    ->setOrderComment() //商店備註
    ->setPaymentMethod() //付款方式 *依照 config 格式傳送*
    ->setCVSCOM() // 物流方式
    ->setLgsType() // 物流型態
    ->setTokenTerm() // 快速付款 token
    ->submit();
```

*此版本1.5由籃新金流回傳後為加密訊息，所以回傳後需要進行解碼！*

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

function callback(Request $request)
{
    $data = NewebPay::decodeFromRequest();
    dd($data);
    // 儲存資料 和 重導向...
}
```

### NewebPay Cancel - 信用卡取消授權

```php
use Ycs77\NewebPay\Facades\NewebPay;

function creditCancel()
{
    return NewebPay::creditCancel(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 `order`->訂單編號，或是 `trade`->藍新交易編號來做申請
    )->submit();
}
```

### NewebPay Close - 信用卡請款

```php
use Ycs77\NewebPay\Facades\NewebPay;

function requestPayment()
{
    return NewebPay::requestPayment(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 `order`->訂單編號，或是 `trade`->藍新交易編號來做申請
    )->submit();
}
```

### NewebPay close - 信用卡退款

```php
use Ycs77\NewebPay\Facades\NewebPay;

function requestRefund()
{
    return NewebPay::requestRefund(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 `order`->訂單編號，或是 `trade`->藍新交易編號來做申請
    )->submit();
}
```

## 參考

[NewebPay Payment API](https://www.newebpay.com/website/Page/content/download_api#1)

## License

[MIT](./LICENSE)

[ico-version]: https://img.shields.io/packagist/v/ycs77/laravel-newebpay?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square
[ico-ci]: https://img.shields.io/travis/ycs77/laravel-newebpay?style=flat-square
[ico-style-ci]: https://github.styleci.io/repos/262404477/shield?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ycs77/laravel-newebpay?style=flat-square

[link-packagist]: https://packagist.org/packages/ycs77/laravel-newebpay
[link-ci]: https://app.travis-ci.com/github/ycs77/laravel-newebpay
[link-style-ci]: https://github.styleci.io/repos/262404477
[link-downloads]: https://packagist.org/packages/ycs77/laravel-newebpay
