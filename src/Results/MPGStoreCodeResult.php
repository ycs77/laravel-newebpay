<?php

namespace Ycs77\NewebPay\Results;

class MPGStoreCodeResult extends Result
{
    /**
     * 繳費超商中英文名稱對照
     */
    protected $storeTypes = [
        1 => '7-11',
        2 => '全家',
        3 => 'OK',
        4 => '萊爾富',
    ];

    /**
     * 繳費代碼
     */
    public function codeNo(): string
    {
        return $this->data['CodeNo'];
    }

    /**
     * 繳費門市類別
     *
     * * **1**: 7-11
     * * **2**: 全家
     * * **3**: OK
     * * **4**: 萊爾富
     */
    public function storeType(): int
    {
        return $this->data['StoreType'];
    }

    /**
     * 繳費超商中文名稱
     */
    public function storeTypeName(): string
    {
        return $this->storeTypes[$this->data['StoreType']] ?? $this->data['StoreType'];
    }

    /**
     * 繳費門市代號
     *
     * 全家回傳門市中文名稱
     */
    public function storeId(): string
    {
        return $this->data['StoreID'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'CodeNo',
            'StoreType',
            'StoreID',
        ];
    }
}
