# Laravel NewebPay - 藍新金流

> Fork from [treerful/laravel-newebpay](https://bitbucket.org/pickone/laravel-newebpay)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![GitHub Tests Action Status][ico-github-action]][link-github-action]
[![Total Downloads][ico-downloads]][link-downloads]

**Laravel NewebPay** 為針對 Laravel 所寫的藍新金流（智付通）金流串接套件。

## 套件功能

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
  - [快速開始](#快速開始)
  - [付款方式](#付款方式)
  - [接收付款結果](#接收付款結果)
  - [取得付款結果的詳細資訊](#取得付款結果的詳細資訊)
  - [ATM/超商取號](#atm超商取號)
- [查詢交易詳情](#查詢交易詳情)
- [信用卡取消授權](#信用卡取消授權)
- [信用卡請退款](#信用卡請退款)
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
NEWEBPAY_ENV=test               # 設定 API 運行環境 (production 或 test)
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

### 快速開始

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

然後設定路由來發送 MPG 多功能付款請求。付款方式預設全部關閉，這裡以最常見的信用卡付款為例，用 `withCredit()` 顯式啟用：

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay', function () {
    return NewebPay::payment()
        ->withOrder('Order'.time())       // 訂單編號
        ->withAmount(120)                 // 交易金額
        ->withItemDescription('我的商品')  // 商品名稱
        ->withEmail('test@example.com')   // 付款人信箱
        ->withCredit()                    // 啟用信用卡付款
        ->withReturnUrl('/pay/callback')  // 支付完成返回網址 (Callback)
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

然後把這個路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/pay/callback',
    ];
}
```

這樣就完成一個最基本的信用卡付款流程了。想開啟更多付款方式，請參考[付款方式](#付款方式)；callback 與 notify 的完整設定，請參考[接收付款結果](#接收付款結果)。

### 付款方式

可依需要啟用信用卡、WebATM／ATM、國民旅遊卡、行動支付、ezPay、超商代碼／條碼等多種付款方式，以下分別說明各自的設定方式。

#### 信用卡

啟用信用卡功能，信用卡可搭配紅利折抵與分期付款：

```php
use Ycs77\NewebPay\Enums\CreditInst;

NewebPay::payment()
    ...
    // 啟用信用卡，預設為一次付清
    ->withCredit()
    // 啟用紅利折抵
    ->withCredit(red: true)
    // 分期付款：3、6 期
    ->withCredit(inst: [CreditInst::P3, CreditInst::P6])
    ->submit();
```

分期參數 `inst` 可傳單一 `CreditInst` 或陣列，選項如下：

| 選項 | 說明 |
|------|------|
| `CreditInst::NONE` | 不啟用分期（預設） |
| `CreditInst::ALL` | 啟用全部分期 |
| `CreditInst::P3` | 分 3 期 |
| `CreditInst::P6` | 分 6 期 |
| `CreditInst::P12` | 分 12 期 |
| `CreditInst::P18` | 分 18 期 |
| `CreditInst::P24` | 分 24 期 |

#### 信用卡記憶卡號

啟用信用卡記憶卡號功能，傳入付款人綁定資料（例如：會員編號、Email），用於綁定付款人與信用卡卡號時使用，格式限英、數字、「.」、「_」、「@」、「-」：

```php
NewebPay::payment()
    ...
    ->withCreditRemember('john@example.com')
    ->submit();
```

信用卡記憶卡號的快速結帳必填欄位預設值為，必填信用卡到期日與背面末三碼，可以修改為只需填寫其中一個欄位：

```php
NewebPay::payment()
    ...
    // 必填信用卡到期日
    ->withCreditRemember('john@example.com', CreditRememberDemand::EXPIRATION_DATE)
    // 必填背面末三碼
    ->withCreditRemember('john@example.com', CreditRememberDemand::CVC)
    ->submit();
```

#### WebATM／ATM 轉帳

```php
NewebPay::payment()
    ...
    // 啟用 WebATM
    ->withWebAtm()
    // 啟用 ATM 轉帳
    ->withAtmTransfer()
    ->submit();
```

WebATM 與 ATM 轉帳可用 `withBank()` 指定顯示於付款頁上的轉帳銀行（此參數為兩者共用，無法個別分開指定），可傳單一 `Bank` 或陣列：

```php
use Ycs77\NewebPay\Enums\Bank;

NewebPay::payment()
    ...
    ->withAtmTransfer()
    ->withBank([Bank::BOT, Bank::HNCB]) // 台灣銀行、華南銀行
    ->submit();
```

可用的銀行有 `Bank::BOT`（台灣銀行）、`Bank::HNCB`（華南銀行）、`Bank::FirstBank`（第一銀行）。若未設定，預設會顯示所有銀行選項。

> [!NOTE]
> 每日 00:00~01:00 為第一銀行例行維護時間，此區間內不會顯示第一銀行選項；若此時僅指定第一銀行一家，將回應 `MPG01027` 錯誤代碼。

#### 國民旅遊卡

可傳入旅遊地區與起訖日期：

```php
use Ycs77\NewebPay\Enums\NTCBLocate;

NewebPay::payment()
    ...
    ->withNationalTravelCard(NTCBLocate::HsinchuCity, '2020-01-01', '2020-12-31')
    ->submit();
```

旅遊地區可使用的選項請參考 `\Ycs77\NewebPay\Enums\NTCBLocate` 類別。

#### 行動支付

啟用行動支付功能，支援 Google Pay、Samsung Pay、LINE Pay、銀聯卡、玉山 Wallet、台灣 Pay，可依需要啟用其中一種或多種：

```php
NewebPay::payment()
    ...
    ->withGooglePay()  // Google Pay
    ->withSamsungPay() // Samsung Pay
    ->withLinePay()    // LINE Pay
    ->withUnionPay()   // 銀聯卡
    ->withEsunWallet() // 玉山 Wallet
    ->withTaiwanPay()  // 台灣 Pay
    ->submit();
```

LINE Pay 可傳入產品圖檔連結，將會顯示於 LINE Pay 付款前的產品圖片區（建議尺寸 84*84 像素，未提供時使用藍新系統預設圖檔）：

```php
NewebPay::payment()
    ...
    ->withLinePay(imageUrl: 'http://example.com/logo.png')
    ->submit();
```

#### ezPay

啟用 ezPay 功能，支援 ezPay 電子錢包、微信支付、支付寶：

```php
NewebPay::payment()
    ...
    ->withEzPay()       // ezPay 電子錢包
    ->withEzPayWeChat() // ezPay 微信支付
    ->withEzPayAlipay() // ezPay 支付寶
    ->submit();
```

#### 超商代碼／條碼繳費

啟用超商代碼或條碼繳費功能：

```php
NewebPay::payment()
    ...
    ->withCvsCode() // 超商代碼繳費
    ->withBarcode() // 條碼繳費
    ->submit();
```

#### 物流設定

設定超商物流相關選項：

```php
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;

NewebPay::payment()
    ...
    // 物流方式
    ->withLogisticsPayment(CVSCOM::NOT_PAY_AND_PAY)
    // 物流型態
    ->withLogisticsType(LgsType::C2C)
    ->submit();
```

#### 交易限制

設定交易的秒數限制和截止天數：

```php
NewebPay::payment()
    ...
    ->withTradeLimit(900)  // 交易秒數限制 (60~900 秒)
    ->withExpireDays(14)   // 交易截止日 (天數，最多 180 天)
    ->submit();
```

#### 其他付款選項

禁止修改 Email：

```php
NewebPay::payment()
    ...
    ->disableEmailModify()
    ->submit();
```

商店備註，最多 300 字：

```php
NewebPay::payment()
    ...
    ->withOrderComment('這是訂單備註')
    ->submit();
```

### 接收付款結果

依付款方式不同，藍新金流會用兩種方式回傳交易結果：

- **即時支付**（可直接在 MPG 頁面完成支付）：信用卡、Google Pay、Samsung Pay、LINE Pay、銀聯卡、玉山 Wallet、台灣 Pay、國民旅遊卡等。
- **非即時支付**（需至超商或實體 ATM 完成支付）：WebATM、ATM 轉帳、超商代碼、超商條碼、超商取貨付款、ezPay 等。

如果只啟用了即時支付的付款方式，則只需要設定 callback；如果只啟用了非即時支付的付款方式，則只需要設定 notify。

如果同時設定了 callback 和 notify，進行部分交易時兩個 API 都會發送訊息，這時就要各司其職：callback 只設定返回給用戶的訊息，而 notify 只負責處理交易的邏輯。

```php
NewebPay::payment()
    ...
    ->withReturnUrl('/pay/callback')    // 支付完成返回網址 (Callback)
    ->withNotifyUrl('/pay/notify')      // 支付通知網址 (Notify)
    ->withCustomerUrl('/pay/customer')  // 商店取號網址
    ->withClientBackUrl('/pay/back')    // 返回按鈕網址
    ->submit();
```

> 若傳入的字串不是完整 URL（例如 `/pay/callback`），套件會自動以 `config('app.url')` 作為前綴補全。若傳入完整 URL（例如 `https://example.com/callback`），則直接使用不做修改。

#### 支付完成返回

信用卡等即時付款方式，付款完成後會直接跳轉回網站，用 callback 回覆給用戶的訊息：

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
```

#### 背景支付通知

ATM、超商等取號付款方式，付款完成是只會透過幕後通知的，用 notify 處理交易邏輯：

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        // 交易失敗，紀錄錯誤訊息...
        return;
    }

    logger('藍新金流 交易資訊 notify', ['result' => $result->toArray()]);

    // 訂單付款成功，處理訂單邏輯...
});
```

然後把這個路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/pay/callback',
        '/pay/notify',
    ];
}
```

#### 取得回傳結果

回傳結果可以使用各個方法來取得需要的資料：

```php
$result = NewebPay::result($request);
$result->status()       // 交易狀態：'SUCCESS' 或錯誤代碼
$result->isSuccess()    // 交易是否成功
$result->isFail()       // 交易是否失敗
$result->message()      // 交易狀態描述：'授權成功'
$result->result()       // 回傳參數 (陣列)
$result->merchantID()   // 藍新金流商店代號：'MS3311...'
$result->amount()       // 交易金額：120
$result->tradeNo()      // 藍新金流交易序號：'23061500000000000'
$result->orderNo()      // 商店訂單編號：'1686759318'
$result->paymentType()  // 付款方式：PaymentType::CREDIT
$result->payTime()      // 支付完成時間：Carbon 實例
$result->ip()           // 交易 IP：'127.0.0.1'
$result->escrowBank()   // 款項保管銀行：'HNCB'
```

取得信用卡支付回傳的詳細資訊：

```php
if ($result->hasCredit()) {
    $credit = $result->credit();
    $credit->authBank()           // 收單金融機構：'CTBC'
    $credit->authBankName()       // 收單金融機構中文名稱：'中國信託銀行'
    $credit->respondCode()        // 金融機構回應碼：'00'
    $credit->auth()               // 授權碼：'115468'
    $credit->card6No()            // 卡號前六碼：'400000'
    $credit->card4No()            // 卡號末四碼：'1111'
    $credit->inst()               // 分期-期別：12
    $credit->instFirst()          // 分期-首期金額：300
    $credit->instEach()           // 分期-每期金額：300
    $credit->ECI()                // ECI 值：'1'
    $credit->tokenUseStatus()     // 信用卡快速結帳使用狀態：0
    $credit->redAmt()             // 紅利折抵後實際金額：null
    $credit->paymentMethod()      // 交易類別：'CREDIT'
    $credit->paymentMethodName()  // 交易類別中文名稱：'台灣發卡機構核發之信用卡'
    $credit->dccAmt()             // 外幣金額：null
    $credit->dccRate()            // 匯率：null
    $credit->dccMarkup()          // 風險匯率：null
    $credit->dccCurrency()        // 幣別：null
    $credit->dccCurrencyCode()    // 幣別代碼：null
}
```

取得 WEBATM、ATM 的詳細資訊：

```php
if ($result->hasAtm()) {
    $atm = $result->atm();
    $atm->payBankCode()        // 付款人金融機構代碼：'809'
    $atm->payerAccount5Code()  // 付款人金融機構帳號末五碼：'12345'
}
```

取得超商代碼繳費回傳的詳細資訊：

```php
if ($result->hasStoreCode()) {
    $storeCode = $result->storeCode();
    $storeCode->codeNo()         // 繳費代碼：'TEST1234567890'
    $storeCode->storeType()      // 繳費門市類別：4
    $storeCode->storeTypeName()  // 繳費超商中文名稱：'萊爾富'
    $storeCode->storeId()        // 繳費門市代號：'S9999'
}
```

取得超商條碼繳費回傳的詳細資訊：

```php
if ($result->hasStoreBarcode()) {
    $storeBarcode = $result->storeBarcode();
    $storeBarcode->barcode1()      // 繳費條碼第一段條碼：'TEST1'
    $storeBarcode->barcode2()      // 繳費條碼第二段條碼：'TEST2'
    $storeBarcode->barcode3()      // 繳費條碼第三段條碼：'TEST3'
    $storeBarcode->repayTimes()    // 付款次數：0
    $storeBarcode->payStore()      // 繳費超商：'SEVEN'
    $storeBarcode->payStoreName()  // 繳費超商中文名稱：'7-11'
}
```

取得超商物流回傳的詳細資訊：

```php
if ($result->hasLgs()) {
    $lgs = $result->lgs();
    $lgs->storeCode()    // 超商門市編號：'019666'
    $lgs->storeName()    // 取貨門市中文名稱：'全家台灣大道店'
    $lgs->storeType()    // 超商類別名稱：'全家'
    $lgs->storeAddr()    // 超商門市地址：'台中市中區台灣大道一段531號'
    $lgs->tradeType()    // 取件交易方式：1（取貨付款）
    $lgs->cvscomName()   // 取貨人姓名：'王小明'
    $lgs->cvscomPhone()  // 取貨人手機號碼：'0900111222'
    $lgs->lgsNo()        // 物流寄件單號：'-'
    $lgs->lgsType()      // 物流型態：LgsType::C2C
}
```

取得跨境支付回傳（包含 ezPay 電子錢包、ezPay 微信支付、ezPay 支付寶）的詳細資訊：

```php
if ($result->hasEzPay()) {
    $ezPay = $result->ezPay();
    $ezPay->isEzPay()      // 是否為 ezPay 交易：true
    $ezPay->channelId()    // 跨境通路類型：'ALIPAY'
    $ezPay->channelName()  // 跨境通路中文名稱：'支付寶'
    $ezPay->channelNo()    // 跨境通路交易序號：'NO0000000001'
}
```

取得玉山 Wallet 回傳的詳細資訊：

```php
if ($result->hasEsunWallet()) {
    $esunWallet = $result->esunWallet();
    $esunWallet->payAmt()     // 實際付款金額：120
    $esunWallet->redDisAmt()  // 紅利折抵金額：0
}
```

取得台灣 Pay 回傳的詳細資訊：

```php
if ($result->hasTaiwanPay()) {
    $taiwanPay = $result->taiwanPay();
    $taiwanPay->payAmt()  // 實際付款金額：120
}
```

### ATM/超商取號

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

    // 自訂取號結果頁面...
});
```

然後把這個路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

```php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        ...
        '/pay/customer',
    ];
}
```

#### 取得取號回傳結果

回傳結果可以使用各個方法來取得需要的資料：

```php
$result = NewebPay::customer($request);
$result->merchantID()   // 藍新金流商店代號：'MS3311...'
$result->amount()       // 交易金額：120
$result->tradeNo()      // 藍新金流交易序號：'23061500000000000'
$result->orderNo()      // 商店訂單編號：'1686763446'
$result->paymentType()  // 付款方式：PaymentType::BARCODE
$result->expireTime()   // 繳費截止日期：Carbon 實例
```

取得 WEBATM、ATM 的詳細資訊：

```php
if ($result->hasAtm()) {
    $atm = $result->atm();
    $atm->bankCode()  // 金融機構代碼：'007'
    $atm->codeNo()    // 繳費代碼：'TestAccount12345'
}
```

取得超商代碼繳費回傳的詳細資訊：

```php
if ($result->hasStoreCode()) {
    $storeCode = $result->storeCode();
    $storeCode->codeNo()  // 繳費代碼：'TEST1234567890'
}
```

取得超商條碼繳費回傳的詳細資訊：

```php
if ($result->hasStoreBarcode()) {
    $storeBarcode = $result->storeBarcode();
    $storeBarcode->barcode1()  // 繳費條碼第一段條碼：'TEST1'
    $storeBarcode->barcode2()  // 繳費條碼第二段條碼：'TEST2'
    $storeBarcode->barcode3()  // 繳費條碼第三段條碼：'TEST3'
}
```

取得超商物流回傳的詳細資訊：

```php
if ($result->hasLgs()) {
    $lgs = $result->lgs();
    $lgs->storeCode()    // 超商門市編號：'019666'
    $lgs->storeName()    // 取貨門市中文名稱：'全家台灣大道店'
    $lgs->storeType()    // 超商類別名稱：'全家'
    $lgs->storeAddr()    // 超商門市地址：'台中市中區台灣大道一段531號'
    $lgs->tradeType()    // 取件交易方式：1（取貨付款）
    $lgs->cvscomName()   // 取貨人姓名：'王小明'
    $lgs->cvscomPhone()  // 取貨人手機號碼：'0900111222'
    $lgs->lgsNo()        // 物流寄件單號：'-'
    $lgs->lgsType()      // 物流型態：LgsType::C2C
}
```

## 查詢交易詳情

從訂單編號和該筆交易的金額來查詢交易詳情：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::query()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->get();

$result->merchantID()   // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()      // 商店訂單編號：'Order001'
$result->tradeNo()      // 藍新金流交易序號：'23061500000000000'
$result->amount()       // 交易金額：1050
$result->paymentType()  // 付款方式：PaymentType::CREDIT
```

查詢組合型商店：

```php
$result = NewebPay::query()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->forCompositeStore()
    ->get();
```

### 取得查詢結果的詳細資訊

取得信用卡支付的詳細資訊：

```php
if ($result->hasCredit()) {
    $credit = $result->credit();
    $credit->respondCode()        // 金融機構回應碼：'00'
    $credit->auth()               // 授權碼：'222111'
    $credit->ECI()                // ECI 值：''
    $credit->closeAmt()           // 請款金額：120
    $credit->closeStatus()        // 請款狀態：0（未請款）
    $credit->backBalance()        // 可退款餘額：120
    $credit->backStatus()         // 退款狀態：0（未退款）
    $credit->respondMsg()         // 授權結果訊息：'授權測試'
    $credit->inst()               // 分期-期別：0
    $credit->instFirst()          // 分期-首期金額：0
    $credit->instEach()           // 分期-每期金額：0
    $credit->paymentMethod()      // 交易類別：'CREDIT'
    $credit->paymentMethodName()  // 交易類別中文名稱：'台灣發卡機構核發之信用卡'
    $credit->card6No()            // 卡號前六碼：'400022'
    $credit->card4No()            // 卡號末四碼：'1111'
    $credit->authBank()           // 收單金融機構：'CTBC'
    $credit->authBankName()       // 收單金融機構中文名稱：'中國信託銀行'
}
```

取得 WEBATM、ATM 的詳細資訊：

```php
if ($result->hasPaymentStatus()) {
    $paymentStatus = $result->paymentStatus();
    $paymentStatus->payInfo()      // 付款資訊：'(822)12345678901234'
    $paymentStatus->expireDate()   // 繳費有效期限：'2023-01-02 23:59:59'
    $paymentStatus->orderStatus()  // 交易狀態：0（未付款）
}
```

取得超商物流的詳細資訊：

```php
if ($result->hasLgs()) {
    $lgs = $result->lgs();
    $lgs->storeCode()  // 超商門市編號：'019666'
    $lgs->storeName()  // 取貨門市中文名稱：'全家台灣大道店'
    $lgs->storeType()  // 超商類別名稱：'全家'
    $lgs->lgsNo()      // 物流訂單編號：'LGS23061500000001'
    $lgs->lgsType()    // 物流型態：LgsType::C2C
}
```

取得數位 Wallet 的詳細資訊：

```php
if ($result->hasDigitalWallet()) {
    $digitalWallet = $result->digitalWallet();
    $digitalWallet->respondCode()        // 金融機構回應碼：'00'
    $digitalWallet->closeAmt()           // 請款金額：120
    $digitalWallet->closeStatus()        // 請款狀態：'0'（未請款）
    $digitalWallet->backBalance()        // 可退款餘額：120（LINE Pay 不支援）
    $digitalWallet->backStatus()         // 退款狀態：'0'（未退款）
    $digitalWallet->respondMsg()         // 授權結果訊息：'交易成功'
    $digitalWallet->paymentMethod()      // 交易類別：'LINEPAY'
    $digitalWallet->paymentMethodName()  // 交易類別中文名稱：'LINE Pay 付款'
    $digitalWallet->authBank()           // 收單金融機構：'Linepay'
    $digitalWallet->authBankName()       // 收單金融機構中文名稱：'LINE Pay'
}
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

$result->merchantID()  // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()     // 商店訂單編號：'Order001'
$result->tradeNo()     // 藍新金流交易序號：'23061500000000000'
$result->amount()      // 取消授權金額：1050
```

或者使用藍新交易編號取消授權：

```php
$result = NewebPay::creditCard()
    ->reverse()
    ->withTrade('23061500000000000') // 藍新金流交易序號
    ->withAmount(1050)
    ->send();
```

## 信用卡請退款

### 信用卡請款

信用卡請款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::creditCard()
    ->capture()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->send();

$result->merchantID()  // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()     // 商店訂單編號：'Order001'
$result->tradeNo()     // 藍新金流交易序號：'23061500000000000'
$result->amount()      // 請款金額：1050
```

取消請款需要加上 `reverse()`：

```php
$result = NewebPay::creditCard()
    ->capture()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->reverse() // 取消請款
    ->send();
```

### 信用卡退款

信用卡退款：

```php
use Ycs77\NewebPay\Facades\NewebPay;

$result = NewebPay::creditCard()
    ->refund()
    ->withOrder('Order001') // 該筆交易的訂單編號
    ->withAmount(1050)      // 該筆交易的金額
    ->send();

$result->merchantID()  // 藍新金流商店代號：'TestMerchantID1234'
$result->orderNo()     // 商店訂單編號：'Order001'
$result->tradeNo()     // 藍新金流交易序號：'23061500000000000'
$result->amount()      // 退款金額：1050
```

取消退款需要加上 `reverse()`：

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
        ->withOrder('Order'.time())              // 訂單編號
        ->withAmount(120)                        // 交易金額
        ->withItemDescription('我的訂閱制商品')   // 商品名稱
        ->withEmail('test@example.com')          // 付款人信箱
        ->withReturnUrl('/pay/period/callback')  // 前景回傳網址 (Callback)
        ->withNotifyUrl('/pay/period/notify')    // 背景通知網址 (Notify)
        ->everyFewDays(2)                        // 每隔 2 天授權一次
        ->times(3)                               // 共授權 3 次
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
NewebPay::period()
    ->create()
    ...
    ->startWithTenDollarAuth()
    ->submit();
```

設定立即執行委託金額授權：

```php
NewebPay::period()
    ->create()
    ...
    ->startWithImmediateAuth()
    ->submit();
```

設定刷卡完之後，不檢查信用卡資訊，也不執行授權：

```php
NewebPay::period()
    ->create()
    ...
    ->startWithoutAuth()
    ->submit();
```

當選擇不授權時，需要設定首期授權日：

```php
NewebPay::period()
    ->create()
    ...
    ->firstChargeAt(2023, 3, 1)
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

    $result->merchantID()         // 藍新金流商店代號：'TestMerchantID1234'
    $result->orderNo()            // 商店訂單編號：'Order001'
    $result->tradeNo()            // 藍新金流交易序號：'23061500000000000'
    $result->periodNo()           // 委託單號：'20200101000000001'
    $result->periodAmount()       // 委託金額：1050
    $result->periodType()         // 委託週期：PeriodType::EVERY_FEW_DAYS（固定天期制）
    $result->authTimes()          // 委託總授權期數：3
    $result->authTime()           // 每期授權時間：10
    $result->dateArray()          // 委託所有授權日期排程：['2020-01-01', '2020-01-11', '2020-01-21']
    $result->cardNo()             // 卡號前六與後四碼：'400000******1111'
    $result->authCode()           // 授權碼：'123456'
    $result->respondCode()        // 銀行回應碼：'00'
    $result->escrowBank()         // 款項保管銀行：'HNCB'
    $result->authBank()           // 收單金融機構：'CTBC'
    $result->authBankName()       // 收單金融機構中文名稱：'中國信託銀行'
    $result->paymentMethod()      // 交易類別：'CREDIT'
    $result->paymentMethodName()  // 交易類別中文名稱：'台灣發卡機構核發之信用卡'

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

    $result->merchantID()         // 藍新金流商店代號：'TestMerchantID1234'
    $result->orderNo()            // 商店訂單編號：'Order001'
    $result->tradeNo()            // 藍新金流交易序號：'23061500000000000'
    $result->periodNo()           // 委託單號：'20200101000000001'
    $result->authAmount()       // 委託單本期授權金額：300
    $result->authDate()          // 委託之本期授權時間：'2020-01-01'
    $result->totalTimes()          // 委託之總授權期數：3
    $result->alreadyTimes()          // 委託之已授權期數，包含授權失敗期數：1
    $result->authCode()           // 授權碼：'123456'
    $result->escrowBank()         // 款項保管銀行：'HNCB'
    $result->authBank()           // 收單金融機構：'CTBC'
    $result->authBankName()       // 收單金融機構中文名稱：'中國信託銀行'
    $result->nextAuthDate()                 // 下期委託授權日期：'2020-01-11'

    // 委託授權成功，處理訂單邏輯...
});
```

然後把這個路徑在 `app/Http/Middleware/VerifyCsrfToken.php` 中排除 CSRF 檢查：

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
    $status = $e->getApiStatus();    // 'MPG01001'
    $message = $e->getApiMessage();  // '商店代號不存在'

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
                'TradeStatus' => '1',
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

- 線上交易─幕前支付技術串接手冊 NDNF-1.0.6 (2022/12/30)
- 信用卡定期定額串接技術手冊 NDNP-1.0.1 (2023/8/17)

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
