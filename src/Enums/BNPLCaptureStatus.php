<?php

namespace Ycs77\NewebPay\Enums;

enum BNPLCaptureStatus: int
{
    /** 未請款 */
    case NOT_CAPTURED = 0;

    /** 請款處理中 */
    case PROCESSING = 1;

    /** 請款完成 */
    case COMPLETED = 2;

    /** 請款失敗 */
    case FAILED = 3;
}
