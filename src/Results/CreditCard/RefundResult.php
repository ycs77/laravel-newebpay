<?php

namespace Ycs77\NewebPay\Results\CreditCard;

use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class RefundResult extends BaseResult
{
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
