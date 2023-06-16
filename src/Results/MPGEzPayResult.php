<?php

namespace Ycs77\NewebPay\Results;

class MPGEzPayResult extends Result
{
    /**
     * 跨境通路中英文名稱對照
     */
    protected $channels = [
        'ALIPAY' =>  '支付寶',
        'WECHATPAY' =>  '微信支付',
        'ACCLINK' =>  '約定連結帳戶',
        'CREDIT' =>  '信用卡',
        'CVS' =>  '超商代碼',
        'P2GEACC' =>  '簡單付電子帳戶轉帳',
        'VACC' =>  'ATM 轉帳',
        'WEBATM' =>  'WebATM 轉帳',
    ];

    /**
     * 確認這筆交易是來自 ezPay 的交易
     */
    public function isEzPay()
    {
        return is_string($this->channelId()) && in_array($this->channelId(), array_keys($this->channels));
    }

    /**
     * 跨境通路類型
     *
     * 該筆交易之跨境收款通路。
     *
     * * **ALIPAY**: 支付寶
     * * **WECHATPAY**: 微信支付
     * * **ACCLINK**: 約定連結帳戶
     * * **CREDIT**: 信用卡
     * * **CVS**: 超商代碼
     * * **P2GEACC**: 簡單付電子帳戶轉帳
     * * **VACC**: ATM 轉帳
     * * **WEBATM**: WebATM 轉帳
     */
    public function channelId(): ?string
    {
        return $this->data['ChannelID'] ?? null;
    }

    /**
     * 跨境通路中文名稱
     */
    public function channelName(): ?string
    {
        return $this->channels[$this->data['ChannelID']] ?? $this->data['ChannelID'];
    }

    /**
     * 跨境通路交易序號
     */
    public function channelNo(): ?string
    {
        return $this->data['ChannelNo'] ?? null;
    }

    /**
     * Define the data keys.
     */
    protected function dataKeys(): array
    {
        return [
            'ChannelID',
            'ChannelNo',
        ];
    }
}
