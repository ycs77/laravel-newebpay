<?php

namespace Ycs77\NewebPay\Enums;

enum PaymentType: string
{
    /** 信用卡付款 */
    case CREDIT = 'CREDIT';

    /** 銀行 ATM 轉帳付款 */
    case VACC = 'VACC';

    /** 網路銀行轉帳付款 */
    case WEBATM = 'WEBATM';

    /** 超商條碼繳費 */
    case BARCODE = 'BARCODE';

    /** 超商代碼繳費 */
    case CVS = 'CVS';

    /** LINE Pay 付款 */
    case LINEPAY = 'LINEPAY';

    /** 玉山 Wallet */
    case ESUNWALLET = 'ESUNWALLET';

    /** 台灣 Pay */
    case TAIWANPAY = 'TAIWANPAY';

    /** 超商取貨付款 */
    case CVSCOM = 'CVSCOM';
}
