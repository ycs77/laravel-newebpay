<?php

namespace Ycs77\NewebPay\Callback;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Results\MPG\PaymentResult;

class MPGCallbackResult
{
    public function __construct(
        protected Factory $factory,
        protected Crypto $crypto
    ) {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));
    }

    /**
     * 解析並回傳交易結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function result(Request $request): PaymentResult
    {
        $data = $request->only(
            'Status', 'MerchantID', 'TradeInfo', 'TradeSha', 'Version', 'EncryptType'
        );

        $data['TradeInfo'] = $this->crypto->decryptByAES($data['TradeInfo']);

        return new PaymentResult($data);
    }
}
