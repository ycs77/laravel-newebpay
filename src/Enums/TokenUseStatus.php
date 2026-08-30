<?php

namespace Ycs77\NewebPay\Enums;

enum TokenUseStatus: int
{
    /** 未使用信用卡快速結帳 */
    case NOT_USED = 0;

    /** 首次設定信用卡快速結帳 */
    case FIRST_SETUP = 1;

    /** 使用信用卡快速結帳 */
    case USED = 2;

    /** 取消信用卡快速結帳 */
    case CANCELED = 9;
}
