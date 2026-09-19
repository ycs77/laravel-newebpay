# 安裝與設定

## 安裝

```bash
composer require ycs77/laravel-newebpay
php artisan vendor:publish --tag=newebpay-config
```

在應用程式 `.env` 設定藍新商店資料：

```ini
NEWEBPAY_ENV=test
NEWEBPAY_MERCHANT_ID=...
NEWEBPAY_MERCHANT_HASH_KEY=...
NEWEBPAY_MERCHANT_HASH_IV=...
```

`NEWEBPAY_ENV` 使用 `test` 或 `production`。開發與自動化測試使用測試商店；正式環境才使用正式商店資料。

## 設定行為

`config/newebpay.php` 可調整：

| 設定 | 用途 |
| --- | --- |
| `lang` | 藍新付款頁預設語系 |
| `with_session_id` | 在使用者返回的付款與取號 URL 帶入 Session 恢復資訊 |
| `timeout` | 套件呼叫藍新 HTTP API 的逾時秒數 |

相對的 `withReturnUrl()`、`withNotifyUrl()`、`withCustomerUrl()` 與 `withClientBackUrl()` 會以 `config('app.url')` 補成完整 URL；傳入完整 URL 時保持原值。

## 機密資料

商店代號、HashKey 與 HashIV 只放在環境變數或部署平台的秘密管理機制。不要提交 `.env`、將金鑰寫入程式碼，或輸出到前端、例外訊息與日誌。
