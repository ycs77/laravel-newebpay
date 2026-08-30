<?php

namespace Ycs77\NewebPay\Enums;

enum EzPayChannel: string
{
    /** 支付寶 */
    case ALIPAY = 'ALIPAY';

    /** 微信支付 */
    case WECHATPAY = 'WECHATPAY';

    /** 約定連結帳戶 */
    case ACCLINK = 'ACCLINK';

    /** 信用卡 */
    case CREDIT = 'CREDIT';

    /** 超商代碼 */
    case CVS = 'CVS';

    /** 簡單付電子帳戶轉帳 */
    case P2GEACC = 'P2GEACC';

    /** ATM 轉帳 */
    case VACC = 'VACC';

    /** WebATM 轉帳 */
    case WEBATM = 'WEBATM';
}
