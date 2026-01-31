<?php

namespace Ycs77\NewebPay\Results\Trade;

use Carbon\Carbon;
use Ycs77\NewebPay\Contracts\CheckCodeVerifiable;
use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class QueryResult extends BaseResult implements CheckCodeVerifiable
{
    use Concerns\HasCheckCode;
    use Concerns\HasMerchantID;
    use Concerns\HasOrderNo;
    use Concerns\HasTradeNo;

    /**
     * 交易金額
     */
    public function amount(): int
    {
        return $this->result['Amt'];
    }

    /**
     * 支付狀態
     *
     * * **0**: 未付款
     * * **1**: 付款成功
     * * **2**: 付款失敗
     * * **3**: 取消付款
     * * **6**: 退款
     */
    public function tradeStatus(): string
    {
        return $this->result['TradeStatus'];
    }

    /**
     * 支付方式
     *
     * * **CREDIT**: 信用卡付款
     * * **VACC**: 銀行 ATM 轉帳付款
     * * **WEBATM**: 網路銀行轉帳付款
     * * **BARCODE**: 超商條碼繳費
     * * **CVS**: 超商代碼繳費
     * * **LINEPAY**: LINE Pay 付款
     * * **ESUNWALLET**: 玉山 Wallet
     * * **TAIWANPAY**: 台灣 Pay
     * * **CVSCOM**: 超商取貨付款
     */
    public function paymentType(): string
    {
        return $this->result['PaymentType'];
    }

    /**
     * 交易建立時間
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function createTime(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->result['CreateTime']);
    }

    /**
     * 支付完成時間
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function payTime(): ?Carbon
    {
        if ($payTime = $this->result['PayTime']) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $payTime);
        }

        return null;
    }

    /**
     * 預計撥款日
     */
    public function fundTime(): ?Carbon
    {
        if ($fundTime = $this->result['FundTime']) {
            return Carbon::createFromFormat('Y-m-d', $fundTime);
        }

        return null;
    }

    /**
     * 實際交易商店代號
     */
    public function shopMerchantId(): ?string
    {
        return $this->result['ShopMerchantID'] ?? null;
    }

    /**
     * 信用卡交易回傳（國外卡、國旅卡、ApplePay、GooglePay、SamsungPay）
     */
    public function credit(): QueryCreditResult
    {
        return new QueryCreditResult($this->result);
    }

    /**
     * 超商代碼、超商條碼、超商取貨付款、LINE Pay、ATM、WebATM 回傳
     */
    public function paymentStatus(): QueryPaymentStatusResult
    {
        return new QueryPaymentStatusResult($this->result);
    }

    /**
     * 超商取貨付款回傳
     */
    public function lgs(): QueryLgsResult
    {
        return new QueryLgsResult($this->result);
    }

    /**
     * 電子錢包（LINE Pay、玉山 Wallet、台灣 Pay）
     */
    public function digitalWallet(): QueryDigitalWalletResult
    {
        return new QueryDigitalWalletResult($this->result);
    }
}
