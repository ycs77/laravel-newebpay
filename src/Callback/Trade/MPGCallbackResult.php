<?php

namespace Ycs77\NewebPay\Callback\Trade;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Exceptions\DecryptException;
use Ycs77\NewebPay\Results\Trade\PaymentResult;

class MPGCallbackResult
{
    public function __construct(
        protected Crypto $crypto,
        protected array $config
    ) {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);
    }

    /**
     * 解析並回傳交易結果。
     *
     * @throws DecryptException
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
