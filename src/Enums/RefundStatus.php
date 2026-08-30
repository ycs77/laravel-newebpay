<?php

namespace Ycs77\NewebPay\Enums;

enum RefundStatus: int
{
    /** 未退款 */
    case NOT_REFUNDED = 0;

    /** 等待提送退款至收單機構 */
    case PENDING_SUBMISSION = 1;

    /** 退款處理中 */
    case PROCESSING = 2;

    /** 退款完成 */
    case COMPLETED = 3;

    /** 退款失敗 */
    case FAILED = 4;
}
