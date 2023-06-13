<?php

namespace Ycs77\NewebPay\Enums;

enum LgsType: string
{
    /**
     * 預設
     *
     * 預設值情況說明：
     * 1. 系統優先啟用［B2C 大宗寄倉］。
     * 2. 若商店設定中未啟用［B2C 大宗寄倉］，則系統將會啟用［C2C 店到店］。
     * 3. 若商店設定中，［B2C 大宗寄倉］與［C2C 店到店］皆未啟用，則支付頁面中將不會出現物流選項。
     */
    case DEFAULT = 'default';

    /** 超商大宗寄倉(目前僅支援統㇐超商) */
    case B2C = 'B2C';

    /** 超商店到店(目前僅支援全家) */
    case C2C = 'C2C';
}
