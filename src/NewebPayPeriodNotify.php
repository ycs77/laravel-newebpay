<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Results\PeriodNotifyResult;

class NewebPayPeriodNotify extends NewebPay
{
    /**
     * 委託每期授權完成回傳結果
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function result(Request $request): PeriodNotifyResult
    {
        $data = $this->decode($request->input('Period'));

        return new PeriodNotifyResult($data);
    }
}
