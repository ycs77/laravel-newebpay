<?php

namespace Ycs77\NewebPay;

use Illuminate\Http\Request;
use Ycs77\NewebPay\Results\PeriodResult;

class NewebPayPeriodResult extends NewebPay
{
    /**
     * 回傳委託授權結果
     *
     * @throws \Ycs77\NewebPay\Exceptions\NewebpayDecodeFailException
     */
    public function result(Request $request): PeriodResult
    {
        $data = $this->decode($request->input('Period'));

        return new PeriodResult($data);
    }
}
