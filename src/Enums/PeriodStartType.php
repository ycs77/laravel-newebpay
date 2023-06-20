<?php

namespace Ycs77\NewebPay\Enums;

enum PeriodStartType: int
{
    /** 立即執行十元授權 */
    case TEN_DOLLARS_NOW = 1;
    /** 立即執行委託金額授權 */
    case AUTHORIZE_NOW = 2;
    /** 不檢查信用卡資訊，不授權 */
    case NO_AUTHORIZE = 3;
}
