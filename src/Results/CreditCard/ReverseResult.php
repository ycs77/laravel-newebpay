<?php

namespace Ycs77\NewebPay\Results\CreditCard;

use Ycs77\NewebPay\Contracts\CheckCodeVerifiable;
use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class ReverseResult extends BaseResult implements CheckCodeVerifiable
{
    use Concerns\HasCheckCode;
    use Concerns\HasMerchantID;
    use Concerns\HasOrderNo;
    use Concerns\HasTradeNo;

    /**
     * 交易金額
     */
    public function amount(): int
    {
        return $this->result['Amt'];
    }
}
