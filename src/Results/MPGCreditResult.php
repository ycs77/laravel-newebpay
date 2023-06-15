<?php

namespace Ycs77\NewebPay\Results;

class MPGCreditResult extends Result
{
    /**
     * 收單金融機構中英文名稱對照
     */
    protected $authBanks = [
        'Esun' => '玉山銀行',
        'Taishin' => '台新銀行',
        'CTBC' => '中國信託銀行',
        'NCCC' => '聯合信用卡中心',
        'CathayBK' => '國泰世華銀行',
        'Citibank' => '花旗銀行',
        'UBOT' => '聯邦銀行',
        'SKBank' => '新光銀行',
        'Fubon' => '富邦銀行',
        'FirstBank' => '第一銀行',
    ];

    /**
     * 收單金融機構
     */
    public function authBank(): string
    {
        return $this->data['AuthBank'];
    }

    /**
     * 收單金融機構中文名稱
     */
    public function authBankText(): string
    {
        return $this->authBanks[$this->data['AuthBank']] ?? $this->data['AuthBank'];
    }

    /**
     * 金融機構回應碼
     */
    public function respondCode(): ?string
    {
        return $this->data['RespondCode'] ?? null;
    }

    /**
     * 授權碼
     */
    public function auth(): ?string
    {
        return $this->data['Auth'] ?? null;
    }

    /**
     * 卡號前六碼
     */
    public function card6No(): ?string
    {
        return $this->data['Card6No'] ?? null;
    }

    /**
     * 卡號末四碼
     */
    public function card4No(): ?string
    {
        return $this->data['Card4No'] ?? null;
    }

    /**
     * 分期-期別
     */
    public function inst(): int
    {
        return $this->data['Inst'];
    }

    /**
     * 分期-首期金額
     */
    public function instFirst(): int
    {
        return $this->data['InstFirst'];
    }

    /**
     * 分期-每期金額
     */
    public function instEach(): int
    {
        return $this->data['InstEach'];
    }

    /**
     * ECI 值
     *
     * 3D 回傳值 eci=1,2,5,6，代表為 3D 交易。
     */
    public function ECI(): ?string
    {
        return $this->data['ECI'] ?? null;
    }

    /**
     * 信用卡快速結帳使用狀態
     *
     * * **0**: 該筆交易為非使用信用卡快速結帳功能。
     * * **1**: 該筆交易為首次設定信用卡快速結帳功能。
     * * **2**: 該筆交易為使用信用卡快速結帳功能。
     * * **9**: 該筆交易為取消信用卡快速結帳功能功能。
     */
    public function tokenUseStatus(): int
    {
        return $this->data['TokenUseStatus'];
    }

    /**
     * 紅利折抵後實際金額
     *
     * 1. 扣除紅利交易折抵後的實際授權金額。
     *    例：1000 元之交易，紅利折抵 60 元，則紅利折抵後實際金額為 940 元。
     * 2. 若紅利點數不足，會有以下狀況：
     *    2-1. 紅利折抵交易失敗，回傳參數數值為 0。
     *    2-2. 紅利折抵交易成功，回傳參數數值為訂單金額。
     *    2-3. 紅利折抵交易是否成功，視該銀行之設定為準。
     * 3. 僅有使用紅利折抵交易時才會回傳此參數。
     * 4. 若紅利折抵掉全部金額，則此欄位回傳參數數值也會是 0，交易成功或交易失敗，請依回傳參數［Status］回覆為準。
     */
    public function redAmt(): ?int
    {
        return $this->data['RedAmt'] ?? null;
    }

    /**
     * 交易類別
     *
     * * **CREDIT**: 台灣發卡機構核發之信用卡
     * * **FOREIGN**: 國外發卡機構核發之卡
     * * **UNIONPAY**: 銀聯卡
     * * **GOOGLEPAY**: GooglePay
     * * **SAMSUNGPAY**: SamsungPay
     * * **DCC**: 動態貨幣轉換
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function paymentMethod(): string
    {
        return $this->data['PaymentMethod'];
    }

    /**
     * 外幣金額
     *
     * * DCC 動態貨幣轉換交易才會回傳的參數
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function dccAmt(): ?float
    {
        return $this->data['DCC_Amt'] ?? null;
    }

    /**
     * 匯率
     *
     * * DCC 動態貨幣轉換交易才會回傳的參數
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function dccRate(): ?float
    {
        return $this->data['DCC_Rate'] ?? null;
    }

    /**
     * 風險匯率
     *
     * * DCC 動態貨幣轉換交易才會回傳的參數
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function dccMarkup(): ?float
    {
        return $this->data['DCC_Markup'] ?? null;
    }

    /**
     * 幣別
     *
     * 例如：USD、JPY、MOP...
     *
     * * DCC 動態貨幣轉換交易才會回傳的參數
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function dccCurrency(): ?string
    {
        return $this->data['DCC_Currency'] ?? null;
    }

    /**
     * 幣別代碼
     *
     * 例如：MOP = 446...
     *
     * * DCC 動態貨幣轉換交易才會回傳的參數
     * * 註：僅支援台新銀行一次付清之代收商店。
     */
    public function dccCurrencyCode(): ?int
    {
        return $this->data['DCC_Currency_Code'] ?? null;
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'AuthBank',
            'RespondCode',
            'Auth',
            'Card6No',
            'Card4No',
            'Inst',
            'InstFirst',
            'InstEach',
            'ECI',
            'TokenUseStatus',
            'RedAmt',
            'PaymentMethod',
            'DCC_Amt',
            'DCC_Rate',
            'DCC_Markup',
            'DCC_Currency',
            'DCC_Currency_Code',
        ];
    }
}
