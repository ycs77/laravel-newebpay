<?php

namespace Ycs77\NewebPay\Enums;

enum PaymentStoreType: int
{
    /** 7-ELEVEN */
    case SEVEN_ELEVEN = 1;

    /** 全家 */
    case FAMILY_MART = 2;

    /** OK mart */
    case OK_MART = 3;

    /** 萊爾富 */
    case HI_LIFE = 4;
}
