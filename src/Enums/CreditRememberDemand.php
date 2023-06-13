<?php

namespace Ycs77\NewebPay\Enums;

enum CreditRememberDemand: int {
    /** 必填信用卡到期日與背面末三碼 */
    case EXPIRATION_DATE_AND_CVC = 1;
    /** 必填信用卡到期日 */
    case EXPIRATION_DATE = 2;
    /** 必填背面末三碼 */
    case CVC = 3;
}
