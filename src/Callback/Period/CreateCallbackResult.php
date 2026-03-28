<?php

namespace Ycs77\NewebPay\Callback\Period;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Results\Period\CreateResult;

class CreateCallbackResult
{
    public function __construct(
        protected Crypto $crypto,
        protected array $config
    ) {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);
    }

    /**
     * 解析並回傳定期定額委託結果。
     *
     * @throws \Ycs77\NewebPay\Exceptions\DecryptException
     */
    public function result(Request $request): CreateResult
    {
        $data = $request->only('Period');

        $data['Period'] = $this->crypto->decryptByAES($data['Period']);

        return new CreateResult($data);
    }
}
