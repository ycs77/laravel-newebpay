<?php

namespace Ycs77\NewebPay\Results;

class BaseResult extends Result
{
    /**
     * 請求狀態
     *
     * 1. 若請求成功，則回傳 SUCCESS。
     * 2. 若請求失敗，則回傳錯誤代碼。
     */
    public function status(): string
    {
        return $this->data['Status'];
    }

    /**
     * 請求是否成功
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'SUCCESS';
    }

    /**
     * 請求是否失敗
     */
    public function isFail(): bool
    {
        return ! $this->isSuccess();
    }

    /**
     * 敘述此次請求狀態
     */
    public function message(): string
    {
        return $this->data['Message'] ?? '';
    }
}
