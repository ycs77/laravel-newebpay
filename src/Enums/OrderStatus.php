<?php

namespace Ycs77\NewebPay\Enums;

enum OrderStatus: int
{
    /** 未付款 */
    case UNPAID = 0;

    /** 已付款 */
    case PAID = 1;

    /** 訂單失敗 */
    case FAILED = 2;

    /** 訂單取消 */
    case CANCELED = 3;

    /** 已退款 */
    case REFUNDED = 6;

    /** 付款中，待銀行確認 */
    case PENDING_BANK_CONFIRMATION = 9;
}
