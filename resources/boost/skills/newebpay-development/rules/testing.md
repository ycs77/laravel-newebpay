# 整合測試

## 模擬 HTTP API 回應

交易查詢、信用卡操作與定期定額修改可用 `NewebPay::fake()`，避免測試時連線藍新。傳入實際呼叫所對應的 Result class：

```php
use Ycs77\NewebPay\Facades\NewebPay;
use Ycs77\NewebPay\Results\Trade\QueryResult;
use Ycs77\NewebPay\Options\Trade\QueryOptions;
use Ycs77\NewebPay\Resources\PaymentQuery;

NewebPay::fake([
    QueryResult::make([
        'Status' => 'SUCCESS',
        'Message' => '查詢成功',
        'Result' => [
            'MerchantID' => 'TestMerchantID1234',
            'MerchantOrderNo' => 'Order001',
            'TradeNo' => '23061500000000000',
            'Amt' => 1050,
            'PaymentType' => 'CREDIT',
        ],
    ]),
]);

$result = NewebPay::query()
    ->withOrder('Order001')
    ->withAmount(1050)
    ->get();

expect($result->orderNo())->toBe('Order001');
```

使用 `NewebPay::assertSent()` 驗證 fake 模式下應用程式送出的請求。傳入實際操作的 Resource class、action，以及檢查 Options 的 callback：

```php
NewebPay::assertSent(PaymentQuery::class, 'query', function (QueryOptions $options) {
    return $options->orderNo === 'Order001'
        && $options->amount === 1050;
});
```

## 測試表單付款

MPG 與定期定額建立的 `submit()` 只產生 HTML form，不會發送 API，因此不能使用 `NewebPay::fake()`。測試應斷言 Route 回應內有藍新表單與加密欄位，或在端對端測試中驗證使用者已前往付款頁。

## 測試 callback 與 notify

建立代表藍新 POST 的 Laravel Request，傳入 `NewebPay::result()`、`customer()`、`periodResult()` 或 `periodNotify()`，再驗證應用程式如何更新訂單或顯示訊息。callback 與 notify 的測試要涵蓋成功與失敗結果；若實作 notify，還要確認同一筆訂單重送時不會再次完成或履約。

測試資料使用藍新測試帳號與假資料；不要在測試、快照或日誌放入正式商店金鑰。
