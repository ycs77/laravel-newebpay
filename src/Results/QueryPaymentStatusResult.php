<?php

namespace Ycs77\NewebPay\Results;

class QueryPaymentStatusResult extends Result
{
    /**
     * 付款資訊
     *
     * 1. 付款方式為超商代碼時，此欄位為超商繳款代碼
     * 2. 付款方式為條碼時，此欄位為繳款條碼。此欄位會將三段條碼資訊用逗號”,”組合後回傳
     * 3. 付款方式為 ATM 轉帳時，此欄位為金融機構的轉帳帳號，括號內為金融機構代碼，例：(031)1234567890
     */
    public function PayInfo(): string
    {
        return $this->data['PayInfo'];
    }

    /**
     * 繳費有效期限
     */
    public function ExpireDate(): string
    {
        return $this->data['ExpireDate'];
    }

    /**
     * 交易狀態
     *
     * * **0**: 未付款
     * * **1**: 已付款
     * * **2**: 訂單失敗
     * * **3**: 訂單取消
     * * **6**: 已退款
     * * **9**: 付款中，待銀行確認
     */
    public function OrderStatus(): int
    {
        return $this->data['OrderStatus'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'PayInfo',
            'ExpireDate',
            'OrderStatus',
        ];
    }
}
