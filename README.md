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
NEWEBPAY_DEBUG=true          # debug 模式

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

<script setup>
defineProps({
  csrf_token: String,
})
</script>
```

然後建立送出付款的路由：

```php
// 路由
Route::post('/pay', 'PaymentController@payment');

// PaymentController.php

use Ycs77\NewebPay\Facades\NewebPay;

class PaymentController
{
    public function payment()
    {
        $no = 'Vanespl_ec_'.time();  // 訂單編號
        $amt = 120;                  // 交易金額
        $desc = '我的商品';           // 商品名稱
        $email = 'test@example.com'; // 付款人信箱

        return NewebPay::payment($no, $amt, $desc, $email)->submit();
    }
}
```

然後是回傳的路由，如果是信用卡之類的付款方式，可以付款後直接跳轉回本網站的，可以只設定 callback。如果是 ATM 的付款方式，需要透過幕後回傳的，可以只設定 notify。

> 記得要在 `.env` 裡設定網址。

```php
// 路由
Route::post('/pay/callback', 'PaymentController@callback');
Route::post('/pay/notify', 'PaymentController@notify');

// PaymentController.php

use Ycs77\NewebPay\Facades\NewebPay;

class PaymentController
{
    public function callback()
    {
        $data = NewebPay::decodeFromRequest();
        dd($data);
        // 儲存資料 和 重導向...
    }

    public function notify()
    {
        $data = NewebPay::decodeFromRequest();
        dd($data);
        // 儲存資料 和 發送付款成功通知...
    }
}
```

然後要把這些路徑排除 CSRF 檢查：

*app/Http/Middleware/VerifyCsrfToken.php*
```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/pay/callback',
        '/pay/notify',
    ];
}
```

因為現在 Laravel 的 Cookie SameSite 預設值是 `Lax`，使用 Form Post 傳到其他網域的網站時不會帶上 Cookie，導致在付款完成後導向回原網站時，會因為讀取不到原本登入的 Cookie，而出現自動登出的問題。因此這裡要加上 `RestoreSessionId` 中間件，會自動從 Callback 網址中讀取加密過的 session id 並設定回原本的 session 狀態：

```php
Route::post('/pay/callback', [PaymentController::class, 'callback'])
    ->middleware(\Ycs77\NewebPay\Http\Middleware\RestoreSessionId::class);
```

> 詳細跟 SameSite 相關可參考: https://developers.google.com/search/blog/2020/01/get-ready-for-new-samesitenone-secure

但如果把 Callback 網址加上 `'auth'` 中間件的話就會失效。這裡需要調整中間件的順序，讓 `RestoreSessionId` 的順序是在 `StartSession` 的下面。預設 Laravel 的 `Kernel` 是不會有 `$middlewarePriority` 屬性，可以在 Laravel Framework 中找到，或直接複製下方到 `app/Http/Kernel.php` 中：

*app/Http/Kernel.php*
```php
class Kernel extends HttpKernel
{
    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Ycs77\NewebPay\Http\Middleware\RestoreSessionId::class, // 必須要將 `RestoreSessionId` 放在 `StartSession` 的下面
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
```

然後就可以正常加上 `'auth'` 中間件來使用了：

```php
Route::middleware('auth')->group(function () {
    Route::post('/pay/callback', [PaymentController::class, 'callback'])
        ->middleware(\Ycs77\NewebPay\Http\Middleware\RestoreSessionId::class);
});
// 或
Route::post('/pay/callback', [PaymentController::class, 'callback'])
    ->middleware([
        'auth',
        \Ycs77\NewebPay\Http\Middleware\RestoreSessionId::class,
    ]);
```

### 測試用帳號

測試環境僅接受以下的測試信用卡號：

* 4000-2211-1111-1111 (一次付清+分期付款)
* 4003-5511-1111-1111 (紅利折抵)

測試卡號有效月年及卡片背面末三碼，可任意填寫。

更多詳細資訊請參考藍新金流 API 文件。s

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

籃新金流回傳後為加密訊息，需要進行解碼：

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
