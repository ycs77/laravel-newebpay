<?php

return [

    /*
     * 開啟藍新金流測試模式 (bool)
     */

    'debug' => env('NEWEBPAY_DEBUG'),

    /*
     * 藍新金流商店代號
     */

    'merchant_id' => env('NEWEBPAY_STORE_ID'),
    'hash_key' => env('NEWEBPAY_STORE_HASH_KEY'),
    'hash_iv' => env('NEWEBPAY_STORE_HASH_IV'),

    /*
     * 回傳格式 JSON/String
     */

    'respond_type' => 'JSON',

    /*
     * 串接版本
     */

    'version' => '1.5',

    /*
     * 語系 zh-tw/en
     */

    'lang_type' => 'zh-tw',

    /*
     * 交易秒數限制 (int)
     *
     * default: 0
     * 0: 不限制
     * 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算
     * 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算
     */

    'trade_limit' => 0,

    /*
     * 繳費有效期限
     *
     * default: 7
     * maxValue: 180
     */

    'expire_date' => 7,

    /*
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 or 443
     * default: null
     */

    'return_url' => env('NEWEBPAY_RETURN_URL') != null ? env('APP_URL').env('NEWEBPAY_RETURN_URL') : null,

    /*
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     * 僅接受 port 80 or 443
     * default: null
     */

    'notify_url' => env('NEWEBPAY_NOTIFY_URL') != null ? env('APP_URL').env('NEWEBPAY_NOTIFY_URL') : null,

    /*
     * 商店取號網址
     *
     * 此參數若為空值，則會顯示取號結果在藍新金流頁面。
     * default: null
     */

    'customer_url' => env('NEWEBPAY_CUSTOMER_URL') != null ? env('APP_URL').env('NEWEBPAY_CUSTOMER_URL') : null,

    /*
     * 付款取消-返回商店網址
     *
     * 當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面
     * default: null
     */

    'client_back_url' => env('NEWEBPAY_CLIENT_BACK_URL') != null ? env('APP_URL').env('NEWEBPAY_CLIENT_BACK_URL') : null,

    /*
     * 付款人電子信箱是否開放修改 (bool)
     *
     * default: true
     */

    'email_modify' => true,

    /*
     * 是否需要登入藍新金流會員 (bool)
     */

    'login_type' => false,

    /*
     * 商店備註
     *
     * 1.限制長度為 300 字。
     * 2.若有提供此參數，將會於 MPG 頁面呈現商店備註內容。
     * default: null
     */

    'order_comment' => null,

    /*
     * 支付方式
     */

    'payment_method' => [

        /*
         * 信用卡支付 (default: true)
         * Enable: 是否啟用信用卡支付
         * CreditRed: 是否啟用紅利
         * InstFlag: 是否啟用分期
         *   0: 不啟用
         *   1: 啟用全部分期
         *   3: 分 3 期
         *   6: 分 6 期
         *   12: 分 12 期
         *   18: 分 18 期
         *   24: 分 24 期
         *   以逗號方式開啟多種分期
         */
        'CREDIT' => [
            'Enable' => true,
            'CreditRed' => false,
            'InstFlag' => 0,
        ],

        // Google Pay (default: false)
        'ANDROIDPAY' => false,

        // Samsung Pay (default: false)
        'SAMSUNGPAY' => false,

        // LINE Pay (default: false)
        'LINEPAY' => false,
        // LINE PAY 產品圖檔連結網址
        //   此連結的圖檔將顯示於 LinePay 付款前的產品圖片區，若無產品圖檔連結網址，會使用藍新系統預設圖檔。
        //   圖片尺寸建議使用 84*84 像素。
        // 'ImageUrl' => 'http://example.com/your-image-url',

        // 銀聯卡支付 (default: false)
        'UNIONPAY' => false,

        // WEBATM 支付 (default: false)
        'WEBATM' => false,

        // ATM 轉帳 (default: false)
        'VACC' => false,

        // 超商代碼繳費支付 (default: false)
        'CVS' => false,

        // 條碼繳費支付 (default: false)
        'BARCODE' => false,

        // 玉山 Walle (default: false)
        'ESUNWALLET' => false,

        // 台灣 Pay (default: false)
        'TAIWANPAY' => false,

        // 簡單付電子錢包 (default: false)
        'EZPAY' => false,

        // 簡單付微信支付 (default: false)
        'EZPWECHAT' => false,

        // 簡單付支付寶 (default: false)
        'EZPALIPAY' => false,
    ],

    /*
     * 付款方式-物流啟用
     *
     * 1 = 啟用超商取貨不付款
     * 2 = 啟用超商取貨付款
     * 3 = 啟用超商取貨不付款及超商取貨付款
     * null = 不開啟
     */
    'CVSCOM' => null,

    /*
     * 物流型態
     *
     * B2C = 超商大宗寄倉(目前僅支援統㇐超商)
     * C2C = 超商店到店(目前僅支援全家)
     * null = 預設
     *
     * 預設值情況說明：
     *   a.系統優先啟用［B2C 大宗寄倉］。
     *   b.若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
     *   c.若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
     */
    'lgs_type' => null,

];
