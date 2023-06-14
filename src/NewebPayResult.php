<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Results\MPGResult;

class NewebPayResult extends NewebPay
{
    /**
     * Newebpay 回傳交易結果
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function result(Request $request): MPGResult
    {
        $data = $request->only(
            'Status', 'MerchantID', 'TradeInfo', 'TradeSha', 'Version', 'EncryptType'
        );

        $data['TradeInfo'] = $this->decode($data['TradeInfo']);

        return new MPGResult($data);
    }
}
