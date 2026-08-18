<?php

namespace Ycs77\NewebPay\Enums;

enum TradeStatus: int
{
    /** 未付款 */
    case UNPAID = 0;

    /** 付款成功 */
    case PAID = 1;

    /** 付款失敗 */
    case FAILED = 2;

    /** 取消付款 */
    case CANCELED = 3;

    /** 退款 */
    case REFUNDED = 6;
}
