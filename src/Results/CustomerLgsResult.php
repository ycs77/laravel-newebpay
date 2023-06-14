<?php

namespace Ycs77\NewebPay\Results;

class CustomerLgsResult extends Result
{
    /**
     * 超商門市編號
     */
    public function storeCode(): string
    {
        return $this->data['StoreCode'];
    }

    /**
     * 取貨門市中文名稱
     */
    public function storeName(): string
    {
        return $this->data['StoreName'];
    }

    /**
     * 超商類別名稱
     *
     * [全家]、[7-ELEVEN]、[萊爾富]、[OK mart]
     */
    public function storeType(): string
    {
        return $this->data['StoreType'];
    }

    /**
     * 超商門市地址
     */
    public function storeAddr(): string
    {
        return $this->data['StoreAddr'];
    }

    /**
     * 取件交易方式
     *
     * * 1: 取貨付款
     * * 3: 取貨不付款
     */
    public function tradeType(): int
    {
        return $this->data['TradeType'];
    }

    /**
     * 取貨人姓名
     */
    public function cvscomName(): string
    {
        return $this->data['CVSCOMName'];
    }

    /**
     * 取貨人手機號碼
     */
    public function cvscomPhone(): string
    {
        return $this->data['CVSCOMPhone'];
    }

    /**
     * 物流寄件單號
     */
    public function lgsNo(): string
    {
        return $this->data['LgsNo'];
    }

    /**
     * 物流型態
     *
     * B2C、C2C
     */
    public function lgsType(): string
    {
        return $this->data['LgsType'];
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'StoreCode',
            'StoreName',
            'StoreType',
            'StoreAddr',
            'TradeType',
            'CVSCOMName',
            'CVSCOMPhone',
            'LgsNo',
            'LgsType',
        ];
    }
}
