# 貢獻指南

歡迎參與貢獻，我們使用 Pull request 的方式來接受貢獻。
在提交 Pull request 前，請先閱讀以下指南。

## 貢獻方式

1. Fork 當前專案
2. 建立新分支
3. 撰寫程式，執行測試，提交 commit 和 push 分支
4. 開啟一個 Pull request 和列出你的修改細節，確保遵循 [該範本](.github/PULL_REQUEST_TEMPLATE.md)

## 專案規範

* 本專案主要使用繁體中文，請盡量使用繁體中文撰寫程式碼註解和文件。但有部分情況例外，比如部分繼承自 Laravel 的 Class 的方法註解，是出自英語的第三方程式碼，視情況可以保留。
* 請確保撰寫程式符合當前使用的 [API 參考文件版本](README.md#參考)。若文件有更新版本，則提交 PR 時請一併更新文件版本。

## 程式碼規範

* 請確保符合由 `composer lint` 指令檢查的程式碼風格。
* 請確保提交一致且有意義的 commit 歷史紀錄，讓 Pull request 中的每個 commit 都具有意義。
* 可使用 [git rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) 來避免合併衝突。
* 更新日誌 (Release notes) 的撰寫方式參考自 [如何維護更新日誌](https://keepachangelog.com/zh-TW/1.1.0/)，但實際寫法請依照專案中的已發布版本的格式來撰寫。

## 安裝專案

Clone 你 Fork 的專案，然後安裝開發相依套件：

```bash
composer install
```

## Lint

格式化程式碼：

```bash
composer lint
```

## Rector

執行 Rector：

```bash
composer refacto
```

## 測試

執行測試：

```bash
composer test
```
