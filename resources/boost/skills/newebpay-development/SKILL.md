---
name: newebpay-development
description: Laravel 藍新金流／NewebPay 整合：付款、回傳與 Notify、交易查詢、信用卡取消授權／請款／退款、定期定額。
---

# Laravel NewebPay 整合

此 skill 適用於在 Laravel 應用程式中使用 `ycs77/laravel-newebpay` 套件。

## 選擇流程

| 需求 | 使用方式 |
| --- | --- |
| 建立一次性付款 | `NewebPay::payment()->…->submit()` |
| 接收付款結果 | `NewebPay::result($request)` |
| 解析 ATM／超商取號 | `NewebPay::customer($request)` |
| 查詢交易 | `NewebPay::query()->…->get()` |
| 信用卡取消授權、請款、退款 | `NewebPay::creditCard()` 後接 `reverse()`、`capture()` 或 `refund()`，再 `…->send()` |
| 建立定期定額 | `NewebPay::period()->create()->…->submit()` |
| 接收定期定額結果 | `periodResult($request)`、`periodNotify($request)` |

## 整合程序

1. 依需求從下方索引讀取規則。
2. 在應用程式的訂單流程填入訂單編號、整數金額、商品資訊與付款方式。
3. 以對應的 `NewebPay` 方法解析藍新 POST；將畫面返回與訂單狀態處理分開。
4. 驗證所選流程的可觀察結果：查詢、信用卡操作、callback 與 notify 要涵蓋成功與失敗；若實作 notify，要確認同一筆訂單重送時不會再次完成或履約；`submit()` 則驗證回應含藍新付款表單與必要加密欄位。

## 規則索引

| 情境 | 讀取 |
| --- | --- |
| MPG、定期定額、付款方式、callback、notify 或 CSRF | [`rules/payment-flows.md`](rules/payment-flows.md) |
| 解析付款、取號或定期定額回傳資料 | [`rules/results-and-callbacks.md`](rules/results-and-callbacks.md) |
| 在應用程式測試金流流程 | [`rules/testing.md`](rules/testing.md) |
| 安裝、環境變數、URL 或 Session 設定 | [`rules/configuration.md`](rules/configuration.md) |

## 固定原則

- 金額一律使用整數。
- 付款與定期定額建立會回傳自動送出的 HTML 表單；不是 HTTP API 回應。
- 加密回傳資料一律交給套件的 `result()`、`customer()`、`periodResult()` 或 `periodNotify()` 解析。
- `ReturnURL` 負責使用者畫面，`NotifyURL` 負責訂單付款與履約邏輯。
