<?php

namespace Ycs77\NewebPay\Results;

class QueryLgsResult extends Result
{
    /**
     * 超商門市編號
     */
    public function storeCode()
    {
        return $this->data['StoreCode'];
    }

    /**
     * 取貨門市中文名稱
     */
    public function storeName()
    {
        return $this->data['StoreName'];
    }

    /**
     * 超商類別名稱
     *
     * * **全家**
     * * **統一**
     * * **萊爾富**
     * * **OK mart**
     */
    public function storeType()
    {
        return $this->data['StoreType'];
    }

    /**
     * 物流訂單編號
     */
    public function lgsNo()
    {
        return $this->data['LgsNo'];
    }

    /**
     * 物流型態
     *
     * * **B2C**: 大宗寄倉
     * * **C2C**: 店到店
     */
    public function lgsType()
    {
        return $this->data['LgsType'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'StoreType',
            'StoreCode',
            'StoreName',
            'LgsNo',
            'LgsType',
        ];
    }
}
