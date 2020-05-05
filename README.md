# Laravel-NewebPay

Laravel-NewebPay 為針對 laravel 所寫的金流套件，主要實作藍新金流（原智付通）功能。

主要實作項目：
* NewebPay MPG - 多功能收款
* NewebPay Cancel - 信用卡取消授權
* NewebPay Close - 信用卡請退款



## Installation

1. 使用 comoposer 安裝：

```
composer require treerful/laravel-newebpay
```

2. 註冊套件:

> Laravel 5.5 以上會自動註冊套件，可以跳過此步驟

在 `config/app.php` 註冊套件：
```
 'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        ...
        ...
        Treerful\NewebPay\Providers\NewebPayServiceProvider::class,
```

在 `config/app.php` 增加別名：

```
 'aliases' => [
     ...
    'Storage' => Illuminate\Support\Facades\Storage::class,
    'URL' => Illuminate\Support\Facades\URL::class,
    'Validator' => Illuminate\Support\Facades\Validator::class,
    'View' => Illuminate\Support\Facades\View::class,

    'NewebPay' => Treerful\NewebPay\NewebPay::class,
 ]
```

3. 發布設置檔案：

```
php artisan vendor:publish --provider="Treerful\NewebPay\Providers\NewebPayServiceProvider"
```

## Usage

### 設定 `.env` 檔
```
// .env

CASH_STORE_ID= ...
CASH_STORE_HASH_KEY= ...
CASH_STORE_HASH_IV= ...
CASH_STORE_DEBUG=true/false

CASH_RETURN_URL= ...
CASH_NOTIFY_URL= ...
CASH_CLIENT_BACK_URL= ...
```

### 設定 `config/newebpay.php`
可依據個人商業使用上做調整。

### 引用、初始化類別：
```
use Treerful\Pay2goInvoice\Invoice;

$newebpay = new NewebPay();
```

### NewebPay MPG - 多功能支付
```
function order() 
{
    $newebpay = new NewebPay();
    return $newebpay->payment(
        no, // 訂單編號
        amt, // 交易金額
        desc, // 交易描述
        email // 付款人信箱
    )->submit();
}
```

基本上一般交易可直接在 `config/newebpay.php`做設定，裡面有詳細的解說，但若遇到特殊情況，可依據個別交易做個別 function 設定。
```
$newebpay = new NewebPay();
return $newebpay->payment(
    no, // 訂單編號
    amt, // 交易金額
    desc, // 交易描述
    email // 付款人信箱
)
->setTespondType() // 回傳格式
->setLangType() // 語言設定
->setTradeLimit() // 交易秒數限制
->setExpireDate() // 交易截止日
->setReturnURL() // 由藍新回傳後前景畫面要接收資料顯示的網址
->setNotifyURL() // 由藍新回傳後背景處理資料的接收網址
->setCutomerURL() // 商店取號網址
->setClientBackURL() // 付款取消後返回的網址
->setEmailModify() // 是否開放 email 修改
->setLoginType() // 是否需要登入智付寶會員
->setOrderComment() //商店備註
->setPaymentMethod() //付款方式 *依照 config 格式傳送*
->setCVSCOM() // 物流方式
->setTokenTerm() // 快速付款 token
->submit();
```

*此版本1.5由籃新金流回傳後為加密訊息，所以回傳後需要進行解碼！* 
```
function returnURL(Request $request)
{
    $retrunData = $request->all();

    $newebpay = new NewebPay();
    $retrunData['data'] = $newebpay->decodeCallback($retrunData['TradeInfo']);

    return $retrunData;
}
```

### NewebPay Cancel - 信用卡取消授權
```
function creditCancel()
{
    $newebpay = new NewebPay();
    return $newebpay->creditCancel(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 order->訂單編號 或是 trade->藍新交易編號來做申請
        )->submit();
}
```

### NewebPay Close - 信用卡請款
```
function requestPayment()
{
    $newebpay = new NewebPay();
    return $newebpay->requestPayment(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 order->訂單編號 或是 trade->藍新交易編號來做申請
        )->submit();
}
```

### NewebPay close - 信用卡退款
```
function requestRefund()
{
    $newebpay = new NewebPay();
    return $newebpay->requestRefund(
        no, // 該筆交易的訂單編號
        amt,  // 該筆交易的金額
        'order' // 可選擇是由 order->訂單編號 或是 trade->藍新交易編號來做申請
        )->submit();
}
```


## Authors
* **Wallase Huang** - *work* - [Github](https://github.com/wallasehuang)
* **Treerful** - [Github](https://github.com/treerful)

## Official Reference
[NewebPay Payment API](https://www.newebpay.com/website/Page/content/download_api#1)

## License
[MIT](https://choosealicense.com/licenses/mit/)
