<?php

namespace Ycs77\NewebPay\Results;

class CustomerStoreBarcodeResult extends Result
{
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
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'Barcode_1',
            'Barcode_2',
            'Barcode_3',
        ];
    }
}
