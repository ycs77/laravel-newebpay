<?php

namespace Ycs77\NewebPay\Enums;

enum BNPLRefundStatus: int
{
    /** 未退款 */
    case NOT_REFUNDED = 0;

    /** 退款處理中 */
    case PROCESSING = 2;

    /** 退款完成 */
    case COMPLETED = 3;

    /** 退款失敗 */
    case FAILED = 4;
}
