<?php

namespace Ycs77\NewebPay\Results;

class CustomerStoreCodeResult extends ResultV1
{
    /**
     * 繳費代碼
     */
    public function codeNo(): string
    {
        return $this->data['CodeNo'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'CodeNo',
        ];
    }
}
