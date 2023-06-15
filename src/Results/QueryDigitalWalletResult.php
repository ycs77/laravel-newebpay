<?php

namespace Ycs77\NewebPay\Results;

class QueryDigitalWalletResult extends Result
{
    /**
     * 交易類別中英文名稱對照
     */
    protected $paymentMethods = [
        'LINEPAY' => 'LINE Pay 付款',
        'ESUNWALLET' => '玉山 Wallet',
        'TAIWANPAY' => '台灣 Pay ',
    ];

    /**
     * 收單金融機構中英文名稱對照
     */
    protected $authBanks = [
        'Linepay' => 'LINE Pay',
        'Esun' => '玉山銀行',
    ];

    /**
     * 金融機構回應碼
     */
    public function respondCode(): ?string
    {
        return $this->data['RespondCode'] ?? null;
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
     * * **1**: 請款申請中 (等待提送請款檔至收單機構)
     * * **2**: 請款處理中
     * * **3**: 請款完成
     * * **4**: 請款失敗
     */
    public function closeStatus(): string
    {
        return $this->data['CloseStatus'];
    }

    /**
     * 可退款餘額
     *
     * 1. 若此筆交易未發動退款，則本欄位回傳值為可退款金額
     * 2. 若此筆交易已發動退款，則本欄位回傳值為可退款餘額
     * 3. 目前不支援 LINE Pay
     */
    public function backBalance(): int
    {
        return $this->data['BackBalance'];
    }

    /**
     * 退款狀態
     *
     * * **0**: 未退款
     * * **1**: 退款申請中 (等待提送退款至收單機構)
     * * **2**: 退款處理中
     * * **3**: 退款完成
     * * **4**: 退款失敗
     */
    public function backStatus(): string
    {
        return $this->data['BackStatus'];
    }

    /**
     * 授權結果訊息
     *
     * 銀行回覆此次電子錢包授權結果狀態
     */
    public function respondMsg(): string
    {
        return $this->data['RespondMsg'];
    }

    /**
     * 交易類別
     *
     * * **LINEPAY**: LINE Pay 付款
     * * **ESUNWALLET**: 玉山 Wallet
     * * **TAIWANPAY**: 台灣 Pay
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
     * 收單金融機構
     *
     * * **Linepay**: LINE Pay
     * * **Esun**: 玉山銀行
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
            'CloseAmt',
            'CloseStatus',
            'BackBalance',
            'BackStatus',
            'RespondMsg',
            'PaymentMethod',
            'AuthBank',
        ];
    }
}
