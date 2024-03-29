# Laravel NewebPay - 藍新金流

> Fork from [treerful/laravel-newebpay](https://bitbucket.org/pickone/laravel-newebpay)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![GitHub Tests Action Status][ico-github-action]][link-github-action]
[![Style CI Build Status][ico-style-ci]][link-style-ci]
[![Total Downloads][ico-downloads]][link-downloads]

Laravel NewebPay 為針對 Laravel 所寫的藍新金流（智付通）金流串接套件。

主要實作項目：

* NewebPay MPG - 多功能收款
* NewebPay Query - 單筆交易查詢
* NewebPay Cancel - 信用卡取消授權
* NewebPay Close - 信用卡請退款
* NewebPay Period - 信用卡定期定額委託
* NewebPay Period Alter Status - 修改委託狀態
* NewebPay Period Alter Amt - 修改委託內容

## 版本需求

* PHP 支援 8.1 以上
* Laravel 版本 9 以上

## 安裝

```
composer require ycs77/laravel-newebpay
```

### 發布設置檔案

```
php artisan vendor:publish --tag=newebpay-config
```

## 註冊藍新金流商店

首先先到藍新金流的網站上註冊帳號 (測試時需註冊測試帳號) 和建立商店。然後在「商店資料設定」中啟用需要使用的金流功能 (測試時可以盡量全部啟用)，並複製商店串接 API 的商店代號、`HashKey` 和 `HashIV`。

設定 `.env` 的商店代號和 HashKey 等：

```
NEWEBPAY_STORE_ID=...        # 貼上 商店代號 (Ex: MS3311...)
NEWEBPAY_STORE_HASH_KEY=...  # 貼上 HashKey
NEWEBPAY_STORE_HASH_IV=...   # 貼上 HashIV
NEWEBPAY_DEBUG=true          # 測試模式
```

更多設定需開啟 `config/newebpay.php` 修改。

## 測試用帳號

測試環境僅接受以下的測試信用卡號：

* 4000-2211-1111-1111 (一次付清+分期付款)
* 4003-5511-1111-1111 (紅利折抵)

測試卡號有效月年及卡片背面末三碼，可任意填寫。

更多詳細資訊請參考[藍新金流 API 文件](https://www.newebpay.com/website/Page/content/download_api)。

## MPG 多功能付款

### 發送付款請求頁面

首先先建立一個頁面，和一個「付款」按鈕：

*routes/web.php*
```php
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

*routes/web.php*
```php
Route::get('/pay', function () {
    return Inertia::render('Pay', [
        'csrfToken' => csrf_token(),
    ]);
});
```

*resources/js/pages/Pay.vue*
```vue
<template>
  <form action="/pay" method="POST">
    <input type="hidden" name="_token" :value="csrfToken">
    <button>付款</button>
  </form>
</template>

<script setup>
defineProps({
  csrfToken: String,
})
</script>
```

然後建立送出付款的路由：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay', function () {
    $no = 'Vanespl_ec_'.time();  // 訂單編號
    $amt = 120;                  // 交易金額
    $desc = '我的商品';           // 商品名稱
    $email = 'test@example.com'; // 付款人信箱

    return NewebPay::payment($no, $amt, $desc, $email)->submit();
});
```

基本上一般交易可直接在 `config/newebpay.php` 做設定，裡面有詳細的解說，但若遇到特殊情況，可依據個別交易設定：

```php
use Ycs77\NewebPay\Facades\NewebPay;

return NewebPay::payment(...)
    ->lang() // 語言設定
    ->tradeLimit() // 交易秒數限制
    ->expireDate() // 交易截止日
    ->returnUrl() // 由藍新回傳後前景畫面要接收資料顯示的網址
    ->notifyUrl() // 由藍新回傳後背景處理資料的接收網址
    ->customerUrl() // 商店取號網址
    ->clientBackUrl() // 付款時點擊「返回按鈕」的網址
    ->emailModify() // 是否開放 email 修改
    ->loginType() // 是否需要登入藍新金流會員
    ->orderComment() // 商店備註
    ->paymentMethod() // 付款方式 *依照 config 格式傳送*
    ->CVSCOM() // 物流方式
    ->lgsType() // 物流型態
    ->submit();
```

### 付款請求回傳結果

送出付款之後當然是要建立回傳的路由，如果是信用卡之類的付款方式，可以付款後直接跳轉回本網站的，可以只設定 callback：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/callback', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return redirect()->to('/pay')->with('error', $result->message());
    }

    // 訂單付款成功，處裡訂單邏輯...

    return redirect()->to('/pay')->with('success', '付款成功');
});
```

如果是 ATM 的付款方式，需要透過幕後回傳的，可以只設定 notify：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return;
    }

    logger('藍新金流 交易資訊 notify', ['result' => $result->data()]);

    // 訂單付款成功，處裡訂單邏輯...
});
```

回傳結果可以使用各個方法來取得需要的資料：

```php
$result = NewebPay::result($request);
$result->data(); // 回傳完整結果
$result->status(); // 交易狀態：若交易付款成功，則回傳 SUCCESS。若交易付款失敗，則回傳錯誤代碼。
$result->isSuccess(); // 交易是否成功
$result->isFail(); // 交易是否失敗
$result->message(); // 敘述此次交易狀態
$result->result(); // 回傳參數
$result->merchantId(); // 藍新金流商店代號
$result->amt(); // 交易金額
$result->tradeNo(); // 藍新金流交易序號
$result->merchantOrderNo(); // 商店訂單編號
$result->respondType(); // 回傳格式
$result->payTime(); // 支付完成時間
$result->ip(); // 交易 IP
$result->escrowBank(); // 款項保管銀行

// 信用卡支付回傳（一次付清、Google Pay、Samaung Pay、國民旅遊卡、銀聯）
if ($result->paymentType() === 'CREDIT') {
    $credit = $result->credit();
    // 參考：\Ycs77\NewebPay\Results\MPGCreditResult
}

// WEBATM、ATM 繳費回傳
if ($result->paymentType() === 'VACC' || $result->paymentType() === 'WEBATM') {
    $atm = $result->atm();
    // 參考：\Ycs77\NewebPay\Results\MPGATMResult
}

// 超商代碼繳費回傳
if ($result->paymentType() === 'CVS') {
    $storeCode = $result->storeCode();
    // 參考：\Ycs77\NewebPay\Results\MPGStoreCodeResult
}

// 超商條碼繳費回傳
if ($result->paymentType() === 'BARCODE') {
    $storeBarcode = $result->storeBarcode();
    // 參考：\Ycs77\NewebPay\Results\MPGStoreBarcodeResult
}

// 超商物流回傳
if ($result->paymentType() === 'CVSCOM') {
    $lgs = $result->lgs();
    // 參考：\Ycs77\NewebPay\Results\MPGLgsResult
}

// 跨境支付回傳 (包含簡單付電子錢包、簡單付微信支付、簡單付支付寶)
$ezPay = $result->ezPay();
if ($ezPay->isEzPay()) {
    // 參考：\Ycs77\NewebPay\Results\MPGEzPayResult
}

// 玉山 Wallet 回傳
if ($result->paymentType() === 'ESUNWALLET') {
    $esunWallet = $result->esunWallet();
    // 參考：\Ycs77\NewebPay\Results\MPGEsunWalletResult
}

// 台灣 Pay 回傳
if ($result->paymentType() === 'TAIWANPAY') {
    $taiwanPay = $result->taiwanPay();
    // 參考：\Ycs77\NewebPay\Results\MPGTaiwanPayResult
}
```

但如果兩個同時設定的話，進行部分交易時兩個 API 都會發送訊息，這時就要各司其職，callback 只設定返回給用戶的訊息，而 notify 只負責處理交易的邏輯：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/callback', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return redirect()->to('/pay')->with('error', $result->message());
    }

    return redirect()->to('/pay')->with('success', '付款成功');
});

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return;
    }

    logger('藍新金流 交易資訊 notify', ['result' => $result->data()]);

    // 訂單付款成功，處裡訂單邏輯...
});
```

設定好之後可以在 `config/newebpay.php` 裡設定網址：

```php
return [

    // 付款完成後導向頁面
    'return_url' => '/pay/callback',

    // 付款完成後的通知連結
    'notify_url' => '/pay/notify',

]
```

還要把這些路徑排除 CSRF 檢查：

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

## ATM/超商條碼/超商代碼取號

預設會直接導向到藍新金流的取號頁面，沒有特別需求不需要自己做。但如果要自訂取號頁面的話，也是可以自己客製調整：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/customer', function (Request $request) {
    $result = NewebPay::customer($request);

    if ($result->isFail()) {
        // 取號錯誤...
        return;
    }

    $result = $result->result();

    // 自訂取號結果頁面...
});
```

在 `config/newebpay.php` 裡設定網址：

```php
return [

    // 商店取號網址
    'customer_url' => '/pay/customer',

]
```

然後要把路徑排除 CSRF 檢查：

*app/Http/Middleware/VerifyCsrfToken.php*
```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        ...
        '/pay/customer',
    ];
}
```

## 單筆交易查詢

從訂單編號和該筆交易的金額來查詢交易詳情：

```php
use Ycs77\NewebPay\Facades\NewebPay;

function query(Request $request)
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::query($no, $amt, $type)->submit();

    if ($result->isSuccess() && $result->verify()) {
        // 查詢成功...

        return response()->json($result->result());
    }

    return response()->json(['message' => $result->message()]);
}
```

## 信用卡取消授權

在尚未請款時可以發動取消信用卡交易：

```php
use Ycs77\NewebPay\Facades\NewebPay;

function cancel()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::cancel($no, $amt, $type)->submit();

    if ($result->isSuccess() && $result->verify()) {
        return response()->json(['message' => '取消授權成功']);
    }

    return response()->json(['message' => $result->message()]);
}
```

## 信用卡請/退款

設定信用卡請款、取消請款、退款、取消退款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

/**
 * 信用卡請款
 */
function request()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::request($no, $amt, $type)->submit();

    if ($result->isSuccess()) {
        return response()->json(['message' => '信用卡請款成功']);
    }

    return response()->json(['message' => $result->message()]);
}

/**
 * 信用卡取消請款
 */
function cancelRequest()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::cancelRequest($no, $amt, $type)->submit();

    if ($result->isSuccess()) {
        return response()->json(['message' => '信用卡取消請款成功']);
    }

    return response()->json(['message' => $result->message()]);
}

/**
 * 信用卡退款
 */
function refund()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::refund($no, $amt, $type)->submit();

    if ($result->isSuccess()) {
        return response()->json(['message' => '信用卡退款成功']);
    }

    return response()->json(['message' => $result->message()]);
}

/**
 * 信用卡取消退款
 */
function cancelRefund()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::cancelRefund($no, $amt, $type)->submit();

    if ($result->isSuccess()) {
        return response()->json(['message' => '信用卡取消退款成功']);
    }

    return response()->json(['message' => $result->message()]);
}
```

或是也可以使用同一個 API 端點來執行請/退款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

/**
 * 信用卡請/退款
 */
function close()
{
    $no = $request->input('no'); // 該筆交易的訂單編號
    $amt = $request->input('amt'); // 該筆交易的金額
    $type = 'order'; // 可選擇是 'order' (訂單編號)，或是 'trade' (藍新交易編號) 來做申請

    $result = NewebPay::close($no, $amt, $type)
        ->closeType($request->query('type')) // 設定請款或退款
        ->cancel($request->boolean('cancel')) // 取消請款或退款
        ->submit();

    if ($result->isSuccess()) {
        return response()->json(['message' => '請求成功']);
    }

    return response()->json(['message' => $result->message()]);
}
```

## 信用卡定期定額委託

### 發送建立委託請求頁面

首先先建立一個頁面，和一個「訂閱」按鈕：

*routes/web.php*
```php
Route::get('/subscribe', function () {
    return view('subscribe');
});
```

*resources/views/subscribe.blade.php*
```html
<form action="/subscribe" method="POST">
    @csrf
    <button>訂閱</button>
</form>
```

Inertia.js 可以參考以下：

*routes/web.php*
```php
Route::get('/subscribe', function () {
    return Inertia::render('Subscribe', [
        'csrfToken' => csrf_token(),
    ]);
});
```

*resources/js/pages/Subscribe.vue*
```vue
<template>
  <form action="/subscribe" method="POST">
    <input type="hidden" name="_token" :value="csrfToken">
    <button>訂閱</button>
  </form>
</template>

<script setup>
defineProps({
  csrfToken: String,
})
</script>
```

然後建立送出付款的路由：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/subscribe', function () {
    $no = now()->timestamp;      // 訂單編號
    $amt = 120;                  // 交易金額
    $desc = '我的訂閱制商品';     // 商品名稱
    $email = 'test@example.com'; // 付款人信箱

    return NewebPay::period($no, $amt, $desc, $email)
        ->everyFewDays(2)
        ->times(3)
        ->submit();
});
```

### 建立委託請求回傳結果

設定建立委託完成後，將頁面導向回原本的網站頁面：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/period/callback', function (Request $request) {
    $result = NewebPay::periodResult($request);

    if ($result->isFail()) {
        return redirect()->to('/pay')->with('error', $result->message());
    }

    return redirect()->to('/pay')->with('success', '付款成功');
});
```

以及設定每期委託授權結果通知：

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/period/notify', function (Request $request) {
    $result = NewebPay::periodNotify($request);

    if ($result->isFail()) {
        Log::error('藍新金流 定期定額 定期交易錯誤', $result->data());

        return;
    }

    // 委託授權成功，處裡訂單邏輯...
});
```

設定好之後可以在 `config/newebpay.php` 裡設定網址：

```php
return [

    'period' => [
        // 建立委託完成後導向頁面
        'return_url' => '/pay/period/callback',

        // 每期委託授權結果通知：
        'notify_url' => '/pay/period/notify',
    ],

]
```

記得要把這些路徑排除 CSRF 檢查：

*app/Http/Middleware/VerifyCsrfToken.php*
```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        ...
        '/pay/period/callback',
        '/pay/period/notify',
    ];
}
```

### 授權週期

若於週期內需授權多次，請以建立多次委託方式執行。

設定此委託於固定天期制授權，輸入數字為間隔天數 2~999。以授權日期隔日起算，以下為每隔 40 天授權一次：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->everyFewDays(40)
    ->times(1)
    ->submit();
```

設定此委託於每週授權，輸入數字為 1~7，代表每週一至週日。以下為每週日授權一次：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->weekly(7)
    ->times(1)
    ->submit();
```

設定此委託於每月授權，輸入數字為 1~31，每月的第幾天執行委託，若當月沒該日期則由該月的最後一天做為扣款日。以下為每月 20 日授權一次：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->monthly(20)
    ->times(1)
    ->submit();
```

設定此委託於每年授權，輸入每年的幾月幾日執行委託。以下為每年 3 月 4 日授權一次：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->yearly(3, 4)
    ->times(1)
    ->submit();
```

### 授權期數

設定授權委託的期數。以下為每月 4 日授權，共授權 6 次，為期 6 個月：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->monthly(4)
    ->times(6)
    ->submit();
```

### 立即執行十元授權

設定立即執行十元授權，以驗證信用卡：

```php
'period' => [
    'start_type' => PeriodStartType::TEN_DOLLARS_NOW,
],
```

### 立即執行委託金額授權

設定立即執行委託金額授權：

```php
'period' => [
    'start_type' => PeriodStartType::AUTHORIZE_NOW,
],
```

### 不檢查信用卡資訊，不授權

設定刷卡完之後，不檢查信用卡資訊，也不執行授權：

```php
'period' => [
    'start_type' => PeriodStartType::NO_AUTHORIZE,
],
```

但需要設定首期授權日：

```php
NewebPay::period($no, $amt, $desc, $email)
    ->everyFewDays(2)
    ->times(3)
    ->firstdate(2023, 3, 1)
    ->submit();
```

## 修改委託狀態

修改委託狀態需要傳入訂單編號、委託單號和委託狀態：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/period/status', function (Request $request) {
    $result = NewebPay::periodStatus($request->input('no'), $request->input('periodNo'), PeriodStatus::TERMINATE)
        ->submit();

    return $result->isSuccess()
        ? back()->with('success', '修改委託狀態成功')
        : back()->withErrors(['no' => $result->message()]);
});
```

委託狀態可以修改成 `PeriodStatus::SUSPEND` (暫停) 和 `PeriodStatus::TERMINATE` (終止) 兩種狀態，設定成暫停之後還可以改成 `PeriodStatus::RESTART` (啟用)，但只要終止委託後就無法再次啟用了。

暫停後再次啟用的委託將於最近一期開始授權。委託暫停後再啟用總期數不變，扣款時間將向後展延至期數滿期。

## 修改委託內容

修改委託內容需要傳入訂單編號、委託單號，和設定要修改成的委託觸發週期和授權次數：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/period/amt', function (Request $request) {
    $result = NewebPay::periodAmt($request->input('no'), $request->input('periodNo'), $request->input('amt'))
        ->everyFewDays(3)
        ->times(10)
        ->submit();

    return $result->isSuccess()
        ? back()->with('success', '修改委託內容成功')
        : back()->withErrors(['no' => $result->message()]);
});
```

## 參考

[NewebPay Payment API](https://www.newebpay.com/website/Page/content/download_api#1)

## 贊助

如果我維護的套件有幫助到你，可以考慮[贊助我](https://www.patreon.com/ycs77)~ 我會很感謝你~ 而且還可以顯示您的大頭貼在我的主要專案中。

<p align="center">
  <a href="https://www.patreon.com/ycs77">
    <img src="https://cdn.jsdelivr.net/gh/ycs77/static/sponsors.svg"/>
  </a>
</p>

<a href="https://www.patreon.com/ycs77">
  <img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron" />
</a>

## License

Under the [MIT LICENSE](LICENSE)

[ico-version]: https://img.shields.io/packagist/v/ycs77/laravel-newebpay?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square
[ico-github-action]: https://img.shields.io/github/actions/workflow/status/ycs77/laravel-newebpay/tests.yml?branch=main&label=tests&style=flat-square
[ico-style-ci]: https://github.styleci.io/repos/262404477/shield?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ycs77/laravel-newebpay?style=flat-square

[link-packagist]: https://packagist.org/packages/ycs77/laravel-newebpay
[link-github-action]: https://github.com/ycs77/laravel-newebpay/actions/workflows/tests.yml?query=branch%3Amain
[link-style-ci]: https://github.styleci.io/repos/262404477
[link-downloads]: https://packagist.org/packages/ycs77/laravel-newebpay
