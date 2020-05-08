<?php

return [

    /*
     * 開啟藍新金流測試模式 (bool)
     */

    'Debug' => env('CASH_STORE_DEBUG'),

    /*
     * 藍新金流商店代號
     */

    'MerchantID' => env('CASH_STORE_ID'),
    'HashKey' => env('CASH_STORE_HASH_KEY'),
    'HashIV' => env('CASH_STORE_HASH_IV'),

    /*
     * 回傳格式 json/string
     */

    'RespondType' => 'JSON',

    /*
     * 串接版本
     */

    'Version' => '1.5',

    /*
     * 語系 zh-tw/en
     */

    'LangType' => 'zh-tw',

    /*
     * 交易秒數限制
     *
     * default: null
     * null: 不限制
     * 秒數下限為 60 秒，當秒數介於 1~59 秒時，會以 60 秒計算
     * 秒數上限為 900 秒，當超過 900 秒時，會 以 900 秒計算
     */

    'TradeLimit' => null,

    /*
     * 繳費有效期限
     *
     * default: 7
     * maxValue: 180
     */

    'ExpireDate' => 7,

    /*
     * 付款完成後導向頁面
     *
     * 僅接受 port 80 or 443
     * default: null
     */

    'ReturnURL' => env('CASH_RETURN_URL') != null ? env('APP_URL') . env('CASH_RETURN_URL') : null,

    /*
     * 付款完成後的通知連結
     *
     * 以幕後方式回傳給商店相關支付結果資料
     * 僅接受 port 80 or 443
     * default: null
     */

    'NotifyURL' => env('CASH_NOTIFY_URL') != null ? env('APP_URL') . env('CASH_NOTIFY_URL') : null,

    /*
     * 商店取號網址
     *
     * 此參數若為空值，則會顯示取號結果在智付寶頁面。
     * default: null
     */

    'CustomerURL' => env('CASH_CUSTOMER_URL') != null ? env('APP_URL') . env('CASH_CUSTOMER_URL') : null,

    /*
     * 付款取消-返回商店網址
     *
     * 當交易取消時，平台會出現返回鈕，使消費者依以此參數網址返回商店指定的頁面
     * default: null
     */

    'ClientBackURL' => env('CASH_CLIENT_BACK_URL') != null ? env('APP_URL') . env('CASH_CLIENT_BACK_URL') : null,

    /*
     * 付款人電子信箱是否開放修改 (bool)
     *
     * default: false
     */

    'EmailModify' => false,

    /*
     * 是否需要登入智付寶會員 (bool)
     */

    'LoginType' => false,

    /*
     * 商店備註
     *
     * 1.限制長度為 300 字。
     * 2.若有提供此參數，將會於 MPG 頁面呈現商店備註內容。
     * default: null
     */

    'OrderComment' => null,

    /*
     * 支付方式
     */

    'PaymentMethod' => [

        /*
         * 信用卡支付 (default: true)
         * Enable: 是否啟用信用卡支付
         * CreditRed: 是否啟用紅利
         * InstFlag: 是否啟用分期
         *
         * 0: 不啟用
         * 1: 啟用全部分期
         * 3: 分 3 期
         * 6: 分 6 期功能
         * 12: 分 12 期功能
         * 18: 分 18 期功能
         * 24: 分 24 期功能
         * 以逗號方式開啟多種分期
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

        // 銀聯卡支付 (default: false)
        'UNIONPAY' => false,

        // WEBATM支付 (default: false)
        'WEBATM' => false,

        // ATM支付 (default: false)
        'VACC' => false,

        // 超商代碼繳費支付 (default: false)
        'CVS' => false,

        // 條碼繳費支付 (default: false)
        'BARCODE' => false,

        // ezPay 電子錢包 (default: false)
        'P2G' => false,
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

];
