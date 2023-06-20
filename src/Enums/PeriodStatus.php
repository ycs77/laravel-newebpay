<?php

namespace Ycs77\NewebPay\Enums;

enum PeriodStatus: string
{
    /** 暫停 */
    case SUSPEND = 'suspend';
    /** 終止 */
    case TERMINATE = 'terminate';
    /** 啟用 */
    case RESTART = 'restart';
}
