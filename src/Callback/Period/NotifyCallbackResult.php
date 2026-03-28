<?php

namespace Ycs77\NewebPay\Callback\Period;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Results\Period\NotifyResult;

class NotifyCallbackResult
{
    public function __construct(
        protected Factory $factory,
        protected Crypto $crypto
    ) {
        $this->crypto->setHashKey($this->factory->config('hash_key'));
        $this->crypto->setHashIv($this->factory->config('hash_iv'));
    }

    /**
     * 解析並回傳每期授權通知結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function result(Request $request): NotifyResult
    {
        $data = $request->only('Period');

        $data['Period'] = $this->crypto->decryptByAES($data['Period']);

        return new NotifyResult($data);
    }
}
