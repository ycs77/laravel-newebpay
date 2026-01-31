<?php

namespace Ycs77\NewebPay\Results\Trade;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class CustomerResult extends BaseResult
{
    use Concerns\HasMerchantID;
    use Concerns\HasOrderNo;
    use Concerns\HasTradeNo;

    /**
     * 敘述此次交易狀態
     */
    public function message(): string
    {
        return $this->data['TradeInfo']['Message'] ?? '';
    }

    /**
     * 交易金額
     */
    public function amount(): int
    {
        return $this->result['Amt'];
    }

    /**
     * 支付方式
     *
     * * **VACC**: 銀行 ATM 轉帳付款
     * * **BARCODE**: 超商條碼繳費
     * * **CVS**: 超商代碼繳費
     * * **CVSCOM**: 超商取貨付款
     */
    public function paymentType(): PaymentType
    {
        return PaymentType::from($this->result['PaymentType']);
    }

    /**
     * 繳費截止日期
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function expireTime(): ?Carbon
    {
        $expireDate = $this->result['ExpireDate'];
        $expireTime = $this->result['ExpireTime'];

        if ($expireDate && $expireTime) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $expireDate.' '.$expireTime);
        }

        return null;
    }

    /**
     * ATM 繳費回傳
     */
    public function atm(): CustomerATMResult
    {
        return new CustomerATMResult($this->result);
    }

    /**
     * 超商代碼繳費回傳
     */
    public function storeCode(): CustomerStoreCodeResult
    {
        return new CustomerStoreCodeResult($this->result);
    }

    /**
     * 超商條碼繳費回傳
     */
    public function storeBarcode(): CustomerStoreBarcodeResult
    {
        return new CustomerStoreBarcodeResult($this->result);
    }

    /**
     * 超商物流回傳
     */
    public function lgs(): CustomerLgsResult
    {
        return new CustomerLgsResult($this->result);
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['TradeInfo']['Result'] ?? [];
    }
}
