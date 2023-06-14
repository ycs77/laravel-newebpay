<?php

namespace Ycs77\NewebPay\Results;

class MPGCrossBorderResult extends Result
{
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
    public function channelId(): int
    {
        return $this->data['ChannelID'];
    }

    /**
     * 跨境通路交易序號
     */
    public function channelNo(): int
    {
        return $this->data['ChannelNo'];
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
