<?php

namespace Ycs77\NewebPay\Results\Concerns;

trait HasCheckCode
{
    /**
     * 檢核碼
     */
    public function checkCode(): string
    {
        return $this->result['CheckCode'];
    }
}
