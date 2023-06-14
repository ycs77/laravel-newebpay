<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Results\CustomerResult;

class NewebPayCustomer extends NewebPay
{
    /**
     * Newebpay 回傳取號結果
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function result(Request $request): CustomerResult
    {
        $data = $request->only(
            'Status', 'MerchantID', 'TradeInfo', 'TradeSha', 'Version', 'EncryptType'
        );

        $data['TradeInfo'] = $this->decode($data['TradeInfo']);

        return new CustomerResult($data);
    }
}
