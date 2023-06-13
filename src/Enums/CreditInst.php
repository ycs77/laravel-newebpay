<?php

namespace Ycs77\NewebPay\Enums;

enum CreditInst: int {
    /** 不啟用分期 */
    case NONE = 0;
    /** 啟用全部分期 */
    case ALL = 1;
    /** 分 3 期 */
    case P3 = 3;
    /** 分 6 期 */
    case P6 = 6;
    /** 分 12 期 */
    case P12 = 12;
    /** 分 18 期 */
    case P18 = 18;
    /** 分 24 期 */
    case P24 = 24;
}
