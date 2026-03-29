<?php

use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\NTCBLocate;

return [

    /*
    |--------------------------------------------------------------------------
    | API 環境設定
    |--------------------------------------------------------------------------
    |
    | 設定 API 運行環境，test 為測試環境，production 為正式環境。
    | 測試階段請使用 test，正式上線時切換為 production。
    |
    */

    'env' => env('NEWEBPAY_ENV', 'test'),

    /*
    |--------------------------------------------------------------------------
    | 藍新金流商店代號和金鑰
    |--------------------------------------------------------------------------
    |
    | 設定藍新金流商店代號和 HashKey、HashIV 值。
    |
    */

    'merchant_id' => env('NEWEBPAY_MERCHANT_ID'),

    'hash_key' => env('NEWEBPAY_MERCHANT_HASH_KEY'),

    'hash_iv' => env('NEWEBPAY_MERCHANT_HASH_IV'),

    /*
    |--------------------------------------------------------------------------
    | 語系
    |--------------------------------------------------------------------------
    |
    | 語系可設定為：
    |
    | - 繁體中文 (`LangType::ZH_TW`)
    | - 英文 (`LangType::EN`)
    | - 日文 (`LangType::JP`)
    |
    */

    'lang' => LangType::ZH_TW,

    /*
    |--------------------------------------------------------------------------
    | 重導向網址自動攜帶 Session ID
    |--------------------------------------------------------------------------
    |
    | 為以 Form Post 導向回商店的網址加上加密過的 Session ID，解決重導向回網站時
    | 自動登出的問題。開啟時會在 `withReturnUrl()` 和 `withCustomerUrl()` 設定的
    | 網址加上。
    |
    */

    'with_session_id' => true,

    /*
    |--------------------------------------------------------------------------
    | 支付方式
    |--------------------------------------------------------------------------
    |
    | 設定商店需要使用的支付方式。
    |
    */

    'payment_methods' => [

        /**
         * 信用卡支付 (default: true)
         *   enabled: 是否啟用信用卡支付
         *   red: 是否啟用紅利
         *   inst: 分期
         *     CreditInst::NONE  不啟用
         *     CreditInst::ALL   啟用全部分期
         *     CreditInst::P3    分 3 期
         *     CreditInst::P6    分 6 期
         *     CreditInst::P12   分 12 期
         *     CreditInst::P18   分 18 期
         *     CreditInst::P24   分 24 期
         *     使用陣列開啟多種分期，例如：[CreditInst::P3, CreditInst::P6]
         */
        'credit' => [
            'enabled' => true,
            'red' => false,
            'inst' => CreditInst::NONE,
        ],

        /** WebATM 支付 (default: false) */
        'webATM' => false,

        /** ATM 轉帳 (default: false) */
        'VACC' => false,

        /**
         * 金融機構
         *   Bank::BOT        台灣銀行
         *   Bank::HNCB       華南銀行
         *   Bank::FirstBank  第一銀行
         *   使用陣列指定 1 個以上的銀行，例如：[Bank::BOT, Bank::HNCB]
         *
         *   此為 WebATM 與 ATM 轉帳 可供付款人選擇轉帳銀行，將顯示於 MPG 頁上。為共用此參數值，無法個別分開指定。
         *
         *   每日的 00:00:00-01:00:00 為第一銀行例行
         *   維護時間，在此時間區間內，將不會顯示［第一銀行］的選項，若商店在此時間區間僅
         *   指定第一銀行一家銀行，將會回應［MPG01027］的錯誤代碼
         */
        'bank' => Bank::ALL,

        /**
         * 信用卡 國民旅遊卡 (default: false)
         *   enabled: 是否啟用 國民旅遊卡 交易
         *   locate: 旅遊地區，可使用地區請參考 \Ycs77\NewebPay\Enums\NTCBLocate 類別
         *   start_date: 國民旅遊卡起始日期
         *   end_date: 國民旅遊卡結束日期
         */
        'NTCB' => [
            'enabled' => false,
            'locate' => NTCBLocate::TaipeiCity,
            'start_date' => '2015-01-01',
            'end_date' => '2015-01-01',
        ],

        /** Google Pay (default: false) */
        'googlePay' => false,

        /** Samsung Pay (default: false) */
        'samsungPay' => false,

        /**
         * LINE Pay (default: false)
         *   enabled: 是否啟用 LINE Pay 支付
         *   產品圖檔連結網址
         *     此連結的圖檔將顯示於 LINE Pay 付款前的產品圖片區，若無產品圖檔連結網址，會使用藍新系統預設圖檔。
         *     圖片尺寸建議使用 84*84 像素。
         */
        'linePay' => [
            'enabled' => false,
            // 'image_url' => 'http://example.com/your-image-url',
        ],

        /** 銀聯卡支付 (default: false) */
        'unionPay' => false,

        /** 玉山 Walle (default: false) */
        'esunWallet' => false,

        /** 台灣 Pay (default: false) */
        'taiwanPay' => false,

        /** 簡單付電子錢包 (default: false) */
        'ezPay' => false,

        /** 簡單付微信支付 (default: false) */
        'ezpWeChat' => false,

        /** 簡單付支付寶 (default: false) */
        'ezpAlipay' => false,

        /** 超商代碼繳費支付 (default: false) */
        'CVS' => false,

        /** 條碼繳費支付 (default: false) */
        'barcode' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 藍新金流 API 連線逾時設定
    |--------------------------------------------------------------------------
    |
    | 設定與 藍新金流平台 API 連線的逾時秒數。
    |
    */

    'timeout' => (int) env('NEWEBPAY_TIMEOUT', 30),

];
