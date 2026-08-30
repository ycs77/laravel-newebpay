<?php

namespace Ycs77\NewebPay\Enums;

enum CaptureStatus: int
{
    /** 未請款 */
    case NOT_CAPTURED = 0;

    /** 等待提送請款至收單機構 */
    case PENDING_SUBMISSION = 1;

    /** 請款處理中 */
    case PROCESSING = 2;

    /** 請款完成 */
    case COMPLETED = 3;

    /** 請款失敗 */
    case FAILED = 4;
}
