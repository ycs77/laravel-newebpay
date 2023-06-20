<?php

use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Enums\PeriodStartType;

return [

    /*
    |--------------------------------------------------------------------------
    | 藍新金流測試模式
    |--------------------------------------------------------------------------
    |
    | 開啟藍新金流測試模式。
    |
    */

    'debug' => env('NEWEBPAY_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | 藍新金流商店代號和金鑰
    |--------------------------------------------------------------------------
    |
    | 設定藍新金流商店代號和 HashKey、HashIV 值。
    |
    */

    'merchant_id' => env('NEWEBPAY_STORE_ID'),
    'hash_key' => env('NEWEBPAY_STORE_HASH_KEY'),
    'hash_iv' => env('NEWEBPAY_STORE_HASH_IV'),

    /*
    |--------------------------------------------------------------------------
    | 串接版本
    |--------------------------------------------------------------------------
    |
    | 設定 API 串接版本。
    |
    */

    'version' => [
        'mpg' => '2.0',

        'query' => '1.3',
        'credit_cancel' => '1.0',
        'credit_close' => '1.1',

        'period' => '1.5',
        'period_status' => '1.0',
        'period_amt' => '1.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | 語系
    |--------------------------------------------------------------------------
    |
    | 語系可設定 "zh-tw", "en", "jp"。
    |
    */

    'lang' => 'zh-tw',

    /*
    |--------------------------------------------------------------------------
    | 交易秒數限制
    |--------------------------------------------------------------------------
    |
    | 預設值為 0。
    |
    | 0: 不限制
    | 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算。
    | 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算。
    |
    */

    'trade_limit' => 0,

    /*
    |--------------------------------------------------------------------------
    | 繳費有效期限
    |--------------------------------------------------------------------------
    |
    | 預設值為 7 天，上限為 180 天。
    |
    */

    'expire_date' => 7,

    /*
    |--------------------------------------------------------------------------
    | 付款完成後導向頁面
    |--------------------------------------------------------------------------
    |
    | 僅接受 port 80 或 443。
    | 例: /pay/callback
    |
    */

    'return_url' => null,

    /*
    |--------------------------------------------------------------------------
    | 付款完成後的通知連結
    |--------------------------------------------------------------------------
    |
    | 以幕後方式回傳給商店相關支付結果資料。
    |
    | 僅接受 port 80 或 443。
    | 例: /pay/notify
    |
    */

    'notify_url' => null,

    /*
    |--------------------------------------------------------------------------
    | 商店取號網址
    |--------------------------------------------------------------------------
    |
    | 如果設定為 null，則會顯示取號結果在藍新金流頁面。
    | 例: /pay/customer
    |
    */

    'customer_url' => null,

    /*
    |--------------------------------------------------------------------------
    | 付款取消時返回商店網址
    |--------------------------------------------------------------------------
    |
    | 當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面。
    |
    */

    'client_back_url' => null,

    /*
    |--------------------------------------------------------------------------
    | 網址加上 Session ID
    |--------------------------------------------------------------------------
    |
    | 為以 Form Post 導向回商店的網址加上加密過的 Session ID，解決重導向回網站時
    | 自動登出的問題。開啟時只會在 `return_url` 和 `customer_url` 網址加上。
    |
    */

    'with_session_id' => true,

    /*
    |--------------------------------------------------------------------------
    | 付款人電子信箱是否開放修改
    |--------------------------------------------------------------------------
    |
    | 設定付款人電子信箱是否開放修改。
    |
    */

    'email_modify' => true,

    /*
    |--------------------------------------------------------------------------
    | 登入藍新金流會員
    |--------------------------------------------------------------------------
    |
    | 是否需要登入藍新金流會員。
    |
    */

    'login_type' => false,

    /*
    |--------------------------------------------------------------------------
    | 商店備註
    |--------------------------------------------------------------------------
    |
    | 1. 商店備註限制長度為 300 字。
    | 2. 若有輸入此參數，將會於 MPG 頁面呈現商店備註內容。
    |
    */

    'order_comment' => null,

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

        /**
         * 信用卡記憶卡號 (default: false)
         *   enabled: 是否啟用信用卡記憶卡號
         *   demand: 指定付款人信用卡快速結帳必填欄位
         *     CreditInst::EXPIRATION_DATE_AND_CVC  必填信用卡到期日與背面末三碼
         *     CreditInst::EXPIRATION_DATE                    必填信用卡到期日
         *     CreditInst::CVC                      必填背面末三碼
         */
        'credit_remember' => [
            'enabled' => false,
            'demand' => CreditRememberDemand::EXPIRATION_DATE_AND_CVC,
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
    | 物流搭配付款方式
    |--------------------------------------------------------------------------
    |
    | CVSCOM::NOT_PAY          啟用超商取貨不付款
    | CVSCOM::PAY              啟用超商取貨付款
    | CVSCOM::NOT_PAY_AND_PAY  啟用超商取貨不付款 及 超商取貨付款
    | CVSCOM::NONE             不開啟
    |
    */

    'CVSCOM' => CVSCOM::NONE,

    /*
    |--------------------------------------------------------------------------
    | 物流型態
    |--------------------------------------------------------------------------
    |
    | LgsType::B2C      超商大宗寄倉(目前僅支援統㇐超商)
    | LgsType::C2C      超商店到店(目前僅支援全家)
    | LgsType::DEFAULT  預設
    |
    | 預設值情況說明：
    | 1. 系統優先啟用［B2C 大宗寄倉］。
    | 2. 若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
    | 3. 若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
    |
    */

    'lgs_type' => LgsType::DEFAULT,

    /*
    |--------------------------------------------------------------------------
    | 信用卡定期定額委託
    |--------------------------------------------------------------------------
    |
    | 和信用卡定期定額委託相關的設定。
    |
    */

    'period' => [

        /**
         * 交易模式
         *   委託成立後，是否立即進行信用卡授權交易，作為檢查信用卡之有效性
         *   PeriodStartType::TEN_DOLLARS_NOW  立即執行十元授權
         *   PeriodStartType::AUTHORIZE_NOW    立即執行委託金額授權
         *   PeriodStartType::NO_AUTHORIZE     不檢查信用卡資訊，不授權
         */
        'start_type' => PeriodStartType::AUTHORIZE_NOW,

        /**
         * 是否開啟付款人資訊
         *   於付款人填寫此委託時，是否需顯示付款人資訊填寫欄位。
         *   付款人資訊填寫欄位包含付款人姓名、付款人電話、付款人手機。
         */
        'payment_info' => false,

        /**
         * 是否開啟收件人資訊
         *   於付款人填寫此委託時，是否需顯示收件人資訊填寫欄位。
         *   收件人資訊填寫欄位包含收件人姓名、收件人電話、收件人手機、收件人地址。
         */
        'order_info' => false,

        /**
         * 返回商店網址
         *   1. 當付款人首次執行信用卡授權交易完成後，以 Form Post 方式導回商店頁。
         *   2. 若此欄位為空值，交易完成後，付款人將停留在藍新金流交易完成頁面。
         */
        'return_url' => null,

        /**
         * 每期授權結果通知
         *   1. 當付款人每期執行信用卡授權交易完成後，以幕後 Post 方式通知商店授權結果。
         *   2. 若此欄位為空值，則不通知商店授權結果。
         */
        'notify_url' => null,

        /**
         * 取消交易時返回商店的網址
         */
        'back_url' => null,
    ],

];
