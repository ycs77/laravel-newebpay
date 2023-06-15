<?php

namespace Ycs77\NewebPay\Results;

class MPGStoreBarcodeResult extends Result
{
    /**
     * 繳費超商中英文名稱對照
     */
    protected $payStores = [
        'SEVEN' => '7-11',
        'FAMILY' => '全家',
        'OK' => 'OK',
        'HILIFE' => '萊爾富',
    ];

    /**
     * 繳費條碼第一段條碼
     */
    public function barcode1(): string
    {
        return $this->data['Barcode_1'];
    }

    /**
     * 繳費條碼第二段條碼
     */
    public function barcode2(): string
    {
        return $this->data['Barcode_2'];
    }

    /**
     * 繳費條碼第三段條碼
     */
    public function barcode3(): string
    {
        return $this->data['Barcode_3'];
    }

    /**
     * 付款次數
     */
    public function repayTimes(): int
    {
        return $this->data['RepayTimes'];
    }

    /**
     * 繳費超商
     *
     * 收款超商的代碼
     * * **SEVEN**: 7-11
     * * **FAMILY**: 全家
     * * **OK**: OK
     * * **HILIFE**: 萊爾富
     */
    public function payStore(): string
    {
        return $this->data['PayStore'];
    }

    /**
     * 繳費超商中文名稱
     */
    public function payStoreName(): string
    {
        return $this->payStores[$this->data['PayStore']] ?? $this->data['PayStore'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'Barcode_1',
            'Barcode_2',
            'Barcode_3',
            'RepayTimes',
            'PayStore',
        ];
    }
}
