<?php

namespace Ycs77\NewebPay\Callback\Period;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Exceptions\DecryptException;
use Ycs77\NewebPay\Exceptions\NewebPayException;
use Ycs77\NewebPay\Results\Period\NotifyResult;

class NotifyCallbackResult
{
    public function __construct(
        protected Crypto $crypto,
        protected array $config
    ) {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);
    }

    /**
     * 解析並回傳每期授權通知結果。
     *
     * @throws DecryptException
     */
    public function result(Request $request): NotifyResult
    {
        $data = $request->only('Period');

        $data['Period'] = $this->crypto->decryptByAES($data['Period']);

        $status = $data['Period']['Status'];
        $message = $data['Period']['Message'];

        if ($status !== 'SUCCESS') {
            throw new NewebPayException($status, $message);
        }

        return new NotifyResult($data);
    }
}
