<?php

namespace Ycs77\NewebPay\Results\MPG;

use Ycs77\NewebPay\Results\Result;

class CustomerStoreCodeResult extends Result
{
    /**
     * 繳費代碼
     */
    public function codeNo(): string
    {
        return $this->data['CodeNo'];
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'CodeNo',
        ];
    }
}
