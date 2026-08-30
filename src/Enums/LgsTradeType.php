<?php

namespace Ycs77\NewebPay\Enums;

enum LgsTradeType: int
{
    /** 取貨付款 */
    case PAY_ON_PICKUP = 1;

    /** 取貨不付款 */
    case PICKUP_WITHOUT_PAYMENT = 3;
}
