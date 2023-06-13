<?php

namespace Ycs77\NewebPay\Enums;

enum Bank: string
{
    /** 支援所有指定銀行 */
    case ALL = 'all';
    /** 台灣銀行 */
    case BOT = 'BOT';
    /** 華南銀行 */
    case HNCB = 'HNCB';
    /** 第一銀行 */
    case FirstBank = 'FirstBank';
}
