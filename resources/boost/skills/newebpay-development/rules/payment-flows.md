# 付款流程

## MPG 一次性付款

MPG 付款會回傳一個自動送出的 HTML form，瀏覽器隨後 POST 至藍新付款頁。Route 必須直接回傳 `submit()` 的結果，不要期待它會回傳 JSON 或交易結果。

```php
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay', function () {
    return NewebPay::payment()
        ->withOrder('Order'.time())
        ->withAmount(120)
        ->withItemDescription('我的商品')
        ->withEmail('customer@example.com')
        ->withCredit()
        ->withReturnUrl('/pay/callback')
        ->withNotifyUrl('/pay/notify')
        ->submit();
});
```

付款方式預設全數關閉，必須以 `withCredit()`、`withAtmTransfer()`、`withWebAtm()`、`withLinePay()`、`withCvsCode()` 或其他對應 `with…()` 方法明確開啟。信用卡分期、銀行與物流選項使用套件提供的 Enum。

## callback 與 notify

- 即時付款完成後可導回 `ReturnURL`；ATM、超商等非即時付款以 `NotifyURL` 通知。
- 同時設定兩者時，callback 只處理使用者看到的成功或失敗畫面。
- 訂單付款、履約與定期授權等業務邏輯放在 notify，避免使用者重複返回時重複處理。

```php
use Illuminate\Http\Request;
use Ycs77\NewebPay\Facades\NewebPay;

Route::post('/pay/callback', function (Request $request) {
    $result = NewebPay::result($request);

    return redirect('/pay')->with(
        $result->isSuccess() ? 'success' : 'error',
        $result->isSuccess() ? '付款成功' : $result->message(),
    );
});

Route::post('/pay/notify', function (Request $request) {
    $result = NewebPay::result($request);

    if ($result->isFail()) {
        return;
    }

    // 依 $result->orderNo() 與 $result->tradeNo() 更新應用程式訂單。
});
```

## 外部 POST 路由

藍新不會帶 Laravel CSRF token。由藍新 POST 的 callback、notify、customer 與定期定額 callback／notify 路由，都必須排除 CSRF：

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->preventRequestForgery(except: [
        '/pay/callback',
        '/pay/notify',
        '/pay/customer',
        '/pay/period/callback',
        '/pay/period/notify',
    ]);
})
```

## 其他付款操作

查詢與信用卡操作會直接取得 Result：

| 操作 | 使用方式 |
| --- | --- |
| 取消授權 | `NewebPay::creditCard()->reverse()->…->send()` |
| 請款 | `NewebPay::creditCard()->capture()->…->send()` |
| 取消請款 | `NewebPay::creditCard()->capture()->reverse()->…->send()` |
| 退款 | `NewebPay::creditCard()->refund()->…->send()` |
| 取消退款 | `NewebPay::creditCard()->refund()->reverse()->…->send()` |

```php
$result = NewebPay::query()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->get();

$refund = NewebPay::creditCard()
    ->refund()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->send();
```

信用卡操作可用商店訂單編號或藍新交易序號查找交易，但兩者只能擇一。定期定額建立同樣回傳付款表單；設定週期與期數後再 `submit()`。
