<?php

namespace Ycs77\NewebPay\Enums;

enum PeriodType: string
{
    /** 固定天期制 */
    case EVERY_FEW_DAYS = 'D';
    /** 每週 */
    case WEEKLY = 'W';
    /** 每月 */
    case MONTHLY = 'M';
    /** 每年 */
    case YEARLY = 'Y';
}
