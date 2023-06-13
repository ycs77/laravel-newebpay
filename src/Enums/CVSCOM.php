<?php

namespace Ycs77\NewebPay\Enums;

enum CVSCOM: int {
    /** 不開啟 */
    case NONE = 0;
    /** 啟用超商取貨不付款 */
    case NOT_PAY = 1;
    /** 啟用超商取貨付款 */
    case PAY = 2;
    /** 啟用超商取貨不付款 及 超商取貨付款 */
    case NOT_PAY_AND_PAY = 3;
}
