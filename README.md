# Laravel NewebPay - 藍新金流

> Fork from [treerful/laravel-newebpay](https://bitbucket.org/pickone/laravel-newebpay)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![GitHub Tests Action Status][ico-github-action]][link-github-action]
[![Total Downloads][ico-downloads]][link-downloads]

**Laravel NewebPay** 為針對 Laravel 所寫的藍新金流（智付通）金流串接套件。

### 套件功能

* 💳 MPG 多功能收款 API
* 🔍 交易查詢 API
* 🚫 信用卡取消授權 API
* 💸 信用卡請退款 API
* 🔁 信用卡定期定額委託 API

## 目錄

- [版本需求](#版本需求)
- [安裝](#安裝)
- [設定](#設定)
- [測試信用卡號](#測試信用卡號)
- [MPG 多功能付款](#mpg-多功能付款)
  - [建立付款流程](#建立付款流程)
  - [自訂付款選項](#自訂付款選項)
  - [接收付款結果](#接收付款結果)
  - [取得付款結果的詳細資訊](#取得付款結果的詳細資訊)
- [ATM/超商取號](#atm超商取號)
- [單筆交易查詢](#單筆交易查詢)
- [信用卡取消授權](#信用卡取消授權)
- [信用卡請款](#信用卡請款)
- [信用卡退款](#信用卡退款)
- [信用卡定期定額委託](#信用卡定期定額委託)
  - [建立委託](#建立委託)
  - [授權週期](#授權週期)
  - [授權期數](#授權期數)
  - [授權起始方式](#授權起始方式)
  - [接收委託結果](#接收委託結果)
  - [修改委託狀態](#修改委託狀態)
  - [修改委託內容](#修改委託內容)
- [錯誤處理](#錯誤處理)
- [單元測試](#單元測試)
- [除錯支援](#除錯支援)
- [參考](#參考)
- [贊助](#贊助)
- [License](#license)

## 版本需求

| 版本 | PHP 版本 | Laravel 版本 |
| --- | --- | --- |
| 1.x | >=8.1 | >=9.x |
| 2.x | >=8.1 | >=9.x |

## 安裝

使用 Composer 安裝套件：

```bash
composer require ycs77/laravel-newebpay
```

發布設置檔案：

```bash
php artisan vendor:publish --tag=newebpay-config
```

## 設定

前往藍新金流的網站上註冊帳號（測試時需註冊測試帳號）和建立商店。然後在「商店資料設定」中啟用需要使用的金流功能（測試時可以盡量全部啟用），並複製商店串接 API 的商店代號、`HashKey` 和 `HashIV`。

設定 `.env` 的商店代號和 HashKey 等參數：

```ini
NEWEBPAY_ENV=test            # 設定 API 運行環境 (production 或 test)
NEWEBPAY_MERCHANT_ID=...        # 貼上 商店代號 (Ex: MS3311...)
NEWEBPAY_MERCHANT_HASH_KEY=...  # 貼上 HashKey
NEWEBPAY_MERCHANT_HASH_IV=...   # 貼上 HashIV
```

`NEWEBPAY_ENV` 可以設定為 `test`（測試環境）或 `production`（正式環境）。

## 測試信用卡號

測試環境僅接受以下的測試信用卡號：

* 4000-2211-1111-1111 (一次付清+分期付款)
* 4003-5511-1111-1111 (紅利折抵)

測試卡號有效月年及卡片背面末三碼，可任意填寫。

## MPG 多功能付款

### 建立付款流程

首先建立一個含有表單的頁面，讓用戶點擊「付款」按鈕後送出 POST 請求：

*resources/views/pay.blade.php*
```html
<form action="/pay" method="POST">
    @csrf
    <button>付款</button>
</form>
```

Inertia.js 可以參考以下：

*resources/js/pages/Pay.vue*
```vue
<template>
  <form action="/pay" method="POST">
    <input type="hidden" name="_token" :value="csrfToken">
    <button>付款</button>
  </form>
</template>

<script setup lang="ts">
defineProps<{
  csrfToken: string
}>()
</script>
```

然後設定路由來發送 MPG 多功能付款請求：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay', function () {
    return NewebPay::payment()
        ->withOrder('Vanespl_ec_'.time())                    // 訂單編號
        ->withAmount(120)                                    // 交易金額
        ->withItemDescription('我的商品')                     // 商品名稱
        ->withEmail('test@example.com')                      // 付款人信箱
        ->withReturnUrl(config('app.url').'/pay/callback')   // 前景回傳網址 (Callback)
        ->withNotifyUrl(config('app.url').'/pay/notify')     // 背景通知網址 (Notify)
        ->submit();
});
```

付款完成後，藍新金流會將結果回傳到指定的網址。信用卡之類可以直接跳轉回網站的付款方式，設定 callback：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/callback', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return redirect()
            ->to('/pay')
            ->with('error', $result->message());
    }

    // 訂單付款成功，處理訂單邏輯...

    return redirect()
        ->to('/pay')
        ->with('success', '付款成功');
});
```

如果是 ATM 的付款方式，需要透過幕後回傳的，設定 notify：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return;
    }

    logger('藍新金流 交易資訊 notify', ['result' => $result->toArray()]);

    // 訂單付款成功，處理訂單邏輯...
});
```

還要把這些路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/pay/callback',
        '/pay/notify',
    ];
}
```

### 自訂付款選項

基本上一般交易可直接在 `config/newebpay.php` 做設定，但若遇到特殊情況，可依據個別交易設定更多付款選項。

**交易限制**

設定交易的秒數限制和截止天數：

```php
NewebPay::payment()
    ...
    ->withTradeLimit(900)  // 交易秒數限制 (60~900 秒)
    ->withExpireDays(14)   // 交易截止日 (天數，最大 180 天)
    ->submit();
```

**回傳網址**

設定付款完成後的回傳網址：

```php
NewebPay::payment()
    ...
    ->withReturnUrl(config('app.url').'/pay/callback')      // 前景回傳網址 (Callback)
    ->withNotifyUrl(config('app.url').'/pay/notify')        // 背景通知網址 (Notify)
    ->withCustomerUrl(config('app.url').'/pay/customer')    // 商店取號網址
    ->withClientBackUrl(config('app.url').'/pay/back')      // 返回按鈕網址
    ->submit();
```

**付款方式**

覆蓋 config 中的付款方式設定，格式與 config 相同：

```php
NewebPay::payment()
    ...
    ->withPaymentMethods([...]) // 付款方式 *依照 config 格式傳送*
    ->submit();
```

**信用卡記憶卡號**

啟用信用卡記憶卡號功能，傳入付款人名稱：

```php
NewebPay::payment()
    ...
    ->withCreditRemember('John Doe')
    ->submit();
```

**其他選項**

```php
NewebPay::payment()
    ...
    ->disableEmailModify()           // 禁止修改 email
    ->withOrderComment('這是訂單備註') // 商店備註 (最大 300 字)
    ->submit();
```

**物流設定**

設定超商物流相關選項：

```php
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;

NewebPay::payment()
    ...
    ->withLogisticsPayment(CVSCOM::NOT_PAY_AND_PAY) // 物流方式
    ->withLogisticsType(LgsType::C2C)               // 物流型態
    ->submit();
```

### 接收付款結果

在[建立付款流程](#建立付款流程)中已設定了基本的 callback 和 notify 路由。如果同時設定了 callback 和 notify，進行部分交易時兩個 API 都會發送訊息，這時就要各司其職，callback 只設定返回給用戶的訊息，而 notify 只負責處理交易的邏輯：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/callback', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return redirect()
            ->to('/pay')
            ->with('error', $result->message());
    }

    return redirect()
        ->to('/pay')
        ->with('success', '付款成功');
});

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return;
    }

    logger('藍新金流 交易資訊 notify', ['result' => $result->toArray()]);

    // 訂單付款成功，處理訂單邏輯...
});
```

回傳結果可以使用各個方法來取得需要的資料：

```php
$result = NewebPay::result($request);
$result->status()          // 交易狀態：'SUCCESS' 或錯誤代碼
$result->isSuccess()       // 交易是否成功
$result->isFail()          // 交易是否失敗
$result->message()         // 交易狀態描述：'授權成功'
$result->result()          // 回傳參數 (陣列)
$result->merchantId()      // 藍新金流商店代號：'MS3311...'
$result->amount()          // 交易金額：120
$result->tradeNo()         // 藍新金流交易序號：'23061500000000000'
$result->orderNo()         // 商店訂單編號：'1686759318'
$result->paymentType()     // 付款方式：PaymentType::CREDIT
$result->payTime()         // 支付完成時間：Carbon 實例
$result->ip()              // 交易 IP：'127.0.0.1'
$result->escrowBank()      // 款項保管銀行：'HNCB'
```

### 取得付款結果的詳細資訊

根據不同的付款方式，可以取得對應的詳細資訊：

```php
use Ycs77\NewebPay\Enums\PaymentType;

// 信用卡支付回傳（一次付清、Google Pay、Samaung Pay、國民旅遊卡、銀聯）
if ($result->paymentType() === PaymentType::CREDIT) {
    $credit = $result->credit();
    // 參考：\Ycs77\NewebPay\Results\Trade\CreditResult
}

// WEBATM、ATM 繳費回傳
if ($result->paymentType() === PaymentType::VACC || $result->paymentType() === PaymentType::WEBATM) {
    $atm = $result->atm();
    // 參考：\Ycs77\NewebPay\Results\Trade\ATMResult
}

// 超商代碼繳費回傳
if ($result->paymentType() === PaymentType::CVS) {
    $storeCode = $result->storeCode();
    // 參考：\Ycs77\NewebPay\Results\Trade\StoreCodeResult
}

// 超商條碼繳費回傳
if ($result->paymentType() === PaymentType::BARCODE) {
    $storeBarcode = $result->storeBarcode();
    // 參考：\Ycs77\NewebPay\Results\Trade\StoreBarcodeResult
}

// 超商物流回傳
if ($result->paymentType() === PaymentType::CVSCOM) {
    $lgs = $result->lgs();
    // 參考：\Ycs77\NewebPay\Results\Trade\LgsResult
}

// 跨境支付回傳 (包含簡單付電子錢包、簡單付微信支付、簡單付支付寶)
$ezPay = $result->ezPay();
// 參考：\Ycs77\NewebPay\Results\Trade\EzPayResult

// 玉山 Wallet 回傳
if ($result->paymentType() === PaymentType::ESUNWALLET) {
    $esunWallet = $result->esunWallet();
    // 參考：\Ycs77\NewebPay\Results\Trade\EsunWalletResult
}

// 台灣 Pay 回傳
if ($result->paymentType() === PaymentType::TAIWANPAY) {
    $taiwanPay = $result->taiwanPay();
    // 參考：\Ycs77\NewebPay\Results\Trade\TaiwanPayResult
}
```

## ATM/超商取號

預設會直接導向到藍新金流的取號頁面，沒有特別需求不需要自己做。但如果要自訂取號頁面的話，也是可以自己客製調整：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/customer', function (Request $request) {
    $result = NewebPay::customer($request);

    if ($result->isFail()) {
        // 取號錯誤...
        return;
    }

    $result->merchantId()  // 藍新金流商店代號：'MS3311...'
    $result->amount()      // 交易金額：120
    $result->tradeNo()     // 藍新金流交易序號：'23061500000000000'
    $result->orderNo()     // 商店訂單編號：'1686763446'
    $result->paymentType() // 付款方式：PaymentType::BARCODE
    $result->expireTime()  // 繳費截止日期：Carbon 實例

    // 根據付款方式取得對應的取號資訊：
    $result->atm()          // ATM 繳費資訊
    $result->storeCode()    // 超商代碼繳費資訊
    $result->storeBarcode() // 超商條碼繳費資訊
    $result->lgs()          // 超商物流資訊

    // 自訂取號結果頁面...
});
```

還要把路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

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

$result = NewebPay::query()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->get();

$result->merchantId() // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()    // 商店訂單編號：'Order001'
$result->tradeNo()    // 藍新金流交易序號：'23061500000000000'
$result->amount()     // 交易金額：1050
```

如果是組合型商店，可以使用 `forCompositeStore()` 來查詢：

```php
$result = NewebPay::query()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->forCompositeStore()
    ->get();
```

## 信用卡取消授權

在尚未請款時可以發動取消信用卡交易。使用訂單編號取消授權：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::creditCard()
    ->reverse()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->send();

$result->merchantId() // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()    // 商店訂單編號：'Order001'
$result->tradeNo()    // 藍新金流交易序號：'23061500000000000'
$result->amount()     // 取消授權金額：1050
```

或者使用藍新交易編號取消授權：

```php
$result = NewebPay::creditCard()
    ->reverse()
    ->withTrade('23061500000000000') // 藍新金流交易序號
    ->withAmount(1050)
    ->send();
```

## 信用卡請款

信用卡請款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::creditCard()
    ->capture()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->send();

$result->merchantId() // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()    // 商店訂單編號：'Order001'
$result->tradeNo()    // 藍新金流交易序號：'23061500000000000'
$result->amount()     // 請款金額：1050
```

取消請款，在請款的基礎上加上 `reverse()`：

```php
$result = NewebPay::creditCard()
    ->capture()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->reverse() // 取消請款
    ->send();
```

## 信用卡退款

信用卡退款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::creditCard()
    ->refund()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->send();

$result->merchantId() // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()    // 商店訂單編號：'Order001'
$result->tradeNo()    // 藍新金流交易序號：'23061500000000000'
$result->amount()     // 退款金額：1050
```

取消退款，在退款的基礎上加上 `reverse()`：

```php
$result = NewebPay::creditCard()
    ->refund()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->reverse() // 取消退款
    ->send();
```

## 信用卡定期定額委託

### 建立委託

建立信用卡定期定額委託的基本範例：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/subscribe', function () {
    return NewebPay::period()
        ->create()
        ->withOrder('Order'.time())                                   // 訂單編號
        ->withAmount(120)                                             // 交易金額
        ->withItemDescription('我的訂閱制商品')                        // 商品名稱
        ->withEmail('test@example.com')                               // 付款人信箱
        ->withReturnUrl(config('app.url').'/pay/period/callback')     // 前景回傳網址 (Callback)
        ->withNotifyUrl(config('app.url').'/pay/period/notify')       // 背景通知網址 (Notify)
        ->everyFewDays(2)                                             // 每隔 2 天授權一次
        ->times(3)                                                    // 共授權 3 次
        ->submit();
});
```

發送建立委託前需要先建立一個含有表單的頁面：

*resources/views/subscribe.blade.php*
```html
<form action="/subscribe" method="POST">
    @csrf
    <button>訂閱</button>
</form>
```

### 授權週期

若於週期內需授權多次，請以建立多次委託方式執行。

設定此委託於固定天期制授權，輸入數字為間隔天數 2~999。以授權日期隔日起算，以下為每隔 40 天授權一次：

```php
NewebPay::period()
    ->create()
    ...
    ->everyFewDays(40)
    ->times(1)
    ->submit();
```

設定此委託於每週授權，輸入數字為 1~7，代表每週一至週日。以下為每週日授權一次：

```php
NewebPay::period()
    ->create()
    ...
    ->weekly(7)
    ->times(1)
    ->submit();
```

設定此委託於每月授權，輸入數字為 1~31，每月的第幾天執行委託，若當月沒該日期則由該月的最後一天做為扣款日。以下為每月 20 日授權一次：

```php
NewebPay::period()
    ->create()
    ...
    ->monthly(20)
    ->times(1)
    ->submit();
```

設定此委託於每年授權，輸入每年的幾月幾日執行委託。以下為每年 3 月 4 日授權一次：

```php
NewebPay::period()
    ->create()
    ...
    ->yearly(3, 4)
    ->times(1)
    ->submit();
```

### 授權期數

設定授權委託的期數。以下為每月 4 日授權，共授權 6 次，為期 6 個月：

```php
NewebPay::period()
    ->create()
    ...
    ->monthly(4)
    ->times(6)
    ->submit();
```

### 授權起始方式

設定立即執行十元授權，以驗證信用卡：

```php
'period' => [
    'start_type' => PeriodStartType::TEN_DOLLARS_NOW,
],
```

設定立即執行委託金額授權：

```php
'period' => [
    'start_type' => PeriodStartType::AUTHORIZE_NOW,
],
```

設定刷卡完之後，不檢查信用卡資訊，也不執行授權：

```php
'period' => [
    'start_type' => PeriodStartType::NO_AUTHORIZE,
],
```

當選擇不授權時，需要設定首期授權日：

```php
NewebPay::period()
    ->create()
    ...
    ->everyFewDays(2)
    ->times(3)
    ->firstChargeAt(2023, 3, 1) // 首期授權日
    ->submit();
```

### 接收委託結果

設定建立委託完成後，將頁面導向回原本的網站頁面：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/period/callback', function (Request $request) {
    $result = NewebPay::periodResult($request);

    if ($result->isFail()) {
        return redirect()->to('/pay')->with('error', $result->message());
    }

    $result->merchantID()   // 藍新金流商店代號：'TestMerchantID1234'
    $result->orderNo()      // 商店訂單編號：'Order001'
    $result->periodNo()     // 委託單號：'20200101000000001'
    $result->periodAmount() // 委託金額：1050

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
        Log::error('藍新金流 定期定額 定期交易錯誤', $result->toArray());

        return;
    }

    $result->merchantID()  // 藍新金流商店代號：'TestMerchantID1234'
    $result->orderNo()     // 商店訂單編號：'Order001'
    $result->authAmount()  // 本期授權金額：1050
    $result->periodNo()    // 委託單號：'20200101000000001'

    // 委託授權成功，處理訂單邏輯...
});
```

記得要把這些路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

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

### 修改委託狀態

修改委託狀態需要傳入訂單編號和委託單號，並呼叫對應的狀態方法：

終止委託：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::period()
    ->alterStatus()
    ->withOrder('Order001')                // 訂單編號
    ->withPeriod('20200101000000001')       // 委託單號
    ->terminate();                         // 終止委託

$result->orderNo()       // 商店訂單編號：'Order001'
$result->periodNo()      // 委託單號：'20200101000000001'
$result->periodStatus()  // 委託狀態：PeriodStatus::TERMINATE
```

暫停委託：

```php
$result = NewebPay::period()
    ->alterStatus()
    ->withOrder('Order001')
    ->withPeriod('20200101000000001')
    ->suspend(); // 暫停委託
```

暫停後重新啟用委託：

```php
$result = NewebPay::period()
    ->alterStatus()
    ->withOrder('Order001')
    ->withPeriod('20200101000000001')
    ->resume(); // 重新啟用委託
```

> [!IMPORTANT]
>
> 委託狀態設定成暫停之後可以改成啟用，但終止委託後就無法再次啟用了。暫停後再次啟用的委託將於最近一期開始授權，總期數不變，扣款時間將向後展延至期數滿期。

### 修改委託內容

修改委託內容需要傳入訂單編號、委託單號，和設定要修改成的委託觸發週期和授權次數：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::period()
    ->alter()
    ->withOrder('Order001')            // 訂單編號
    ->withPeriod('20200101000000001')  // 委託單號
    ->withAmount(1000)                 // 新的委託金額
    ->everyFewDays(3)                  // 新的授權週期
    ->times(10)                        // 新的授權次數
    ->send();

$result->orderNo()       // 商店訂單編號：'Order001'
$result->periodNo()      // 委託單號：'20200101000000001'
$result->periodAmount()  // 新的委託金額：1000
```

## 錯誤處理

當藍新金流 API 回傳失敗的回應時，會拋出 `NewebPayException` 例外，可以取得藍新金流的錯誤代碼和錯誤訊息進行進一步處理：

```php
use Ycs77\NewebPay\Exceptions\NewebPayException;

try {
    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->get();
} catch (NewebPayException $e) {
    $status = $e->getApiStatus(); // 'MPG01001'
    $message = $e->getApiMessage(); // '商店代號不存在'

    // 記錄錯誤日誌...
    logger()->error($e->getMessage(), $e->context());

    // 顯示錯誤訊息給使用者...
    return response()->json([
        'error' => $message,
    ], 400);
}
```

## 單元測試

在單元測試中，可以使用 `NewebPay::fake()` 模擬 API 回應，這邊要模擬交易查詢回應，因此使用 `QueryResult` 來建立模擬回應資料：

```php
<?php

use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\Trade\QueryOptions;
use Ycs77\NewebPay\Resources\PaymentQuery;
use Ycs77\NewebPay\Results\Trade\QueryResult;

test('can query trade', function () {
    // 模擬交易查詢 API 回應
    NewebPay::fake([
        // 模擬交易查詢回應
        // 需要使用實際呼叫的 Result 類別來建立模擬回應
        //
        // 因為下面使用 NewebPay::query()->get() 來查詢交易
        // 因此模擬的回應類別需要使用 QueryResult 類別
        QueryResult::make([
            'Status' => 'SUCCESS',
            'Message' => '查詢成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
                'TradeStatus' => 1,
                'PaymentType' => 'CREDIT',
                'CreateTime' => '2023-01-01 00:00:00',
                'PayTime' => '2023-01-01 00:00:00',
                'CheckCode' => '123456789',
                'FundTime' => '2023-01-01',
                'RespondCode' => '00',
                'Auth' => '222111',
                'ECI' => '',
                'CloseAmt' => 120,
                'CloseStatus' => 0,
                'BackBalance' => 120,
                'BackStatus' => 0,
                'RespondMsg' => '授權測試',
                'Inst' => 0,
                'InstFirst' => 0,
                'InstEach' => 0,
                'PaymentMethod' => 'CREDIT',
                'Card6No' => '400022',
                'Card4No' => '1111',
                'AuthBank' => 'CTBC',
            ],
        ]),
    ]);

    // 測試發送交易查詢請求
    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->get();

    // 斷言發送參數
    //
    // 因為上面是呼叫 NewebPay::query()
    //
    // 因此斷言的資源類別和方法名稱分別是：
    // - PaymentQuery::class 是 query() 方法回傳的資源類別
    // - 'query' 是呼叫的方法名稱
    NewebPay::assertSent(PaymentQuery::class, 'query', function (QueryOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->amount === 1050;
    });

    expect($result->orderNo())->toBe('Order001')
        ->and($result->tradeNo())->toBe('23061500000000000')
        ->and($result->amount())->toBe(1050);
});
```

信用卡請款的模擬範例，因為呼叫的是 `NewebPay::creditCard()->capture()`，因此模擬回應使用 `CaptureResult`，斷言的資源類別則是 `CreditCard`：

```php
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Options\CreditCard\CaptureOptions;
use Ycs77\NewebPay\Resources\CreditCard;
use Ycs77\NewebPay\Results\CreditCard\CaptureResult;

test('can capture credit card', function () {
    NewebPay::fake([
        CaptureResult::make([
            'Status' => 'SUCCESS',
            'Message' => '請款成功',
            'Result' => [
                'MerchantID' => 'TestMerchantID1234',
                'Amt' => 1050,
                'TradeNo' => '23061500000000000',
                'MerchantOrderNo' => 'Order001',
            ],
        ]),
    ]);

    $result = NewebPay::creditCard()
        ->capture()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->send();

    NewebPay::assertSent(CreditCard::class, 'capture', function (CaptureOptions $options) {
        return $options->orderNo === 'Order001'
            && $options->amount === 1050
            && $options->reverse === false;
    });

    expect($result->orderNo())->toBe('Order001')
        ->and($result->amount())->toBe(1050);
});
```

> [!WARNING]
> 不支援模擬 MPG 多功能付款的 `submit()` 方法，因為該方法是直接產生跳轉表單資料，實際上不會發送 API 請求。

## 除錯支援

當發生錯誤時，請協助提供請求與回應的除錯資料，以便更快速地定位問題：

```php
use Ycs77\NewebPay\Options\Options;

$result = NewebPay::query()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->onPreparedOptions(function (Options $options) {
        dd($options->toArray()); // 查看請求參數資料
    })
    ->get();

dd($result->toArray()); // 查看回應資料
```

- 使用 `$options->toArray()` 方法可檢視發送至 API 的請求資料
- 透過 `$result->toArray()` 可檢視 API 的回應資料內容
- 當發生錯誤時，請在提交 issue 時一併提供請求與回應的除錯資料

## 參考

[API文件下載 | 藍新金流服務平台](https://www.newebpay.com/website/Page/content/download_api#1)

當前參考文件版本：

- 線上交易─幕前支付技術串接手冊
- 信用卡定期定額串接技術手冊

## 相關專案

- [agriweather/laravel-ezpay-invoice](https://github.com/Agriweather/laravel-ezpay-invoice)：Laravel 的 ezPay 電子發票整合套件

## 貢獻專案

歡迎參與貢獻專案，請參考 [貢獻指南](CONTRIBUTING.md) 文件。

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
[ico-github-action]: https://img.shields.io/github/actions/workflow/status/ycs77/laravel-newebpay/tests.yml?branch=2.x&label=tests&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ycs77/laravel-newebpay?style=flat-square

[link-packagist]: https://packagist.org/packages/ycs77/laravel-newebpay
[link-github-action]: https://github.com/ycs77/laravel-newebpay/actions/workflows/tests.yml?query=branch%3A2.x
[link-downloads]: https://packagist.org/packages/ycs77/laravel-newebpay
