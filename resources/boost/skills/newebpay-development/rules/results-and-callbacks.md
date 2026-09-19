# callback 與回傳結果

## 使用正確解析方法

| 藍新 POST 類型 | 解析方法 |
| --- | --- |
| MPG 付款 callback 或 notify | `NewebPay::result($request)` |
| ATM、超商取號 | `NewebPay::customer($request)` |
| 定期定額建立 callback | `NewebPay::periodResult($request)` |
| 定期定額每期授權通知 | `NewebPay::periodNotify($request)` |

將 Laravel `Request` 原樣傳入套件。套件會處理解密與回傳格式；應用程式不應自行讀取或解密藍新的加密欄位。

## 判斷結果

MPG 與取號結果先使用 `isSuccess()` 或 `isFail()`，失敗時以 `message()` 顯示或記錄原因。成功後，常用資料包括：

```php
$result->orderNo();      // 商店訂單編號
$result->tradeNo();      // 藍新交易序號
$result->amount();       // 整數金額
$result->paymentType();  // PaymentType Enum
$result->payTime();      // Carbon|null
```

不同付款方式才會有不同細節。先用 `has…()` 判斷，再讀取對應資料：

```php
if ($result->hasCredit()) {
    $credit = $result->credit();
    $authorization = $credit->auth();
}

if ($result->hasAtm()) {
    $bankCode = $result->atm()->payBankCode();
}
```

超商代碼、條碼、物流、ezPay、玉山 Wallet、台灣 Pay 與交易查詢也各有相應的 `has…()` 與細節方法。依實際付款方式讀取，避免假設所有 Result 都有相同欄位。

## 定期定額與例外

定期定額建立結果和每期通知使用各自的解析方法。成功後可讀取委託單號、授權期數與授權資料；每期通知則可讀取本期金額、已授權期數與下期授權日。

查詢、信用卡操作與定期定額 API 失敗時可能拋出 `NewebPayException`。需要顯示或紀錄 API 錯誤時，取得狀態與訊息： 

```php
use Ycs77\NewebPay\Exceptions\NewebPayException;

try {
    $result = NewebPay::query()
        ->withOrder('Order001')
        ->withAmount(1050)
        ->get();
} catch (NewebPayException $e) {
    $status = $e->getApiStatus();
    $message = $e->getApiMessage();
}
```
