<?php

namespace Ycs77\NewebPay\Callback\Trade;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Exceptions\DecryptException;
use Ycs77\NewebPay\Results\Trade\CustomerResult;

class MPGCustomerResult
{
    public function __construct(
        protected Crypto $crypto,
        protected array $config
    ) {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);
    }

    /**
     * 解析並回傳付款取號結果。
     *
     * @throws DecryptException
     */
    public function result(Request $request): CustomerResult
    {
        $data = $request->only(
            'Status', 'MerchantID', 'TradeInfo', 'TradeSha', 'Version', 'EncryptType'
        );

        $data['TradeInfo'] = $this->crypto->decryptByAES($data['TradeInfo']);

        return new CustomerResult($data);
    }
}
