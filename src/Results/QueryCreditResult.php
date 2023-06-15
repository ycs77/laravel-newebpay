<?php

namespace Ycs77\NewebPay\Results;

class QueryCreditResult extends Result
{
    /**
     * 交易類別中英文名稱對照
     */
    protected $paymentMethods = [
        'CREDIT' => '台灣發卡機構核發之信用卡',
        'FOREIGN' => '國外發卡機構核發之卡',
        'NTCB' => '國民旅遊卡',
        'UNIONPAY' => '銀聯卡',
        'APPLEPAY' => 'ApplePay',
        'GOOGLEPAY' => 'GooglePay',
        'SAMSUNGPAY' => 'SamsungPay',
    ];

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
     * ECI 值
     *
     * 3D 回傳值 eci=1,2,5,6，代表為 3D 交易。
     */
    public function ECI(): ?string
    {
        return $this->data['ECI'] ?? null;
    }

    /**
     * 請款金額
     */
    public function closeAmt(): int
    {
        return $this->data['CloseAmt'];
    }

    /**
     * 請款狀態
     *
     * * **0**: 未請款
     * * **1**: 等待提送請款至收單機構
     * * **2**: 請款處理中
     * * **3**: 請款完成
     */
    public function closeStatus(): int
    {
        return $this->data['CloseStatus'];
    }

    /**
     * 可退款餘額
     *
     * 1. 若此筆交易未發動退款，則本欄位回傳值為可退款金額
     * 2. 若此筆交易已發動退款，則本欄位回傳值為可退款餘額
     */
    public function backBalance(): int
    {
        return $this->data['BackBalance'];
    }

    /**
     * 退款狀態
     *
     * * **0**: 未退款
     * * **1**: 等待提送退款至收單機構
     * * **2**: 退款處理中
     * * **3**: 退款完成
     */
    public function backStatus(): int
    {
        return $this->data['BackStatus'];
    }

    /**
     * 授權結果訊息
     *
     * 銀行回覆此次信用卡授權結果狀態
     */
    public function respondMsg(): string
    {
        return $this->data['RespondMsg'];
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
     * 交易類別
     *
     * * **CREDIT**: 台灣發卡機構核發之信用卡
     * * **FOREIGN**: 國外發卡機構核發之卡
     * * **NTCB**: 國民旅遊卡
     * * **UNIONPAY**: 銀聯卡
     * * **APPLEPAY**: ApplePay
     * * **GOOGLEPAY**: GooglePay
     * * **SAMSUNGPAY**: SamsungPay
     */
    public function paymentMethod(): string
    {
        return $this->data['PaymentMethod'];
    }

    /**
     * 收單金融機構中文名稱
     */
    public function paymentMethodName(): string
    {
        return $this->paymentMethods[$this->data['PaymentMethod']] ?? $this->data['PaymentMethod'];
    }

    /**
     * 卡號前六碼
     */
    public function card6No(): string
    {
        return $this->data['Card6No'];
    }

    /**
     * 卡號末四碼
     */
    public function card4No(): string
    {
        return $this->data['Card4No'];
    }

    /**
     * 收單金融機構
     *
     * * **Esun**: 玉山銀行
     * * **Taishin**: 台新銀行
     * * **CTBC**: 中國信託銀行
     * * **NCCC**: 聯合信用卡中心
     * * **CathayBK**: 國泰世華銀行
     * * **Citibank**: 花旗銀行
     * * **UBOT**: 聯邦銀行
     * * **SKBank**: 新光銀行
     * * **Fubon**: 富邦銀行
     * * **FirstBank**: 第一銀行
     */
    public function authBank(): string
    {
        return $this->data['AuthBank'];
    }

    /**
     * 收單金融機構中文名稱
     */
    public function authBankName(): string
    {
        return $this->authBanks[$this->data['AuthBank']] ?? $this->data['AuthBank'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'RespondCode',
            'Auth',
            'ECI',
            'CloseAmt',
            'CloseStatus',
            'BackBalance',
            'BackStatus',
            'RespondMsg',
            'Inst',
            'InstFirst',
            'InstEach',
            'PaymentMethod',
            'Card6No',
            'Card4No',
            'AuthBank',
        ];
    }
}
