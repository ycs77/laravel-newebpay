<?php

namespace Ycs77\NewebPay\Results\Trade;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\BaseResult;
use Ycs77\NewebPay\Results\Concerns;

class PaymentResult extends BaseResult
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
    public function paymentType(): PaymentType
    {
        return PaymentType::from($this->result['PaymentType']);
    }

    /**
     * 支付完成時間
     *
     * 當使用超商取貨服務時，本欄位的值會以空值回傳
     *
     * @throws InvalidFormatException
     */
    public function payTime(): ?Carbon
    {
        if ($payTime = $this->result['PayTime']) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $payTime);
        }

        return null;
    }

    /**
     * 交易 IP
     */
    public function ip(): string
    {
        return $this->result['IP'];
    }

    /**
     * 款項保管銀行
     *
     * 如商店是直接與收單機構簽約的閘道模式如：支付寶-玉山銀行、ezPay 電子錢包、LINE Pay，
     * 當使用信用卡支付時，本欄位的值會以空值回傳。
     */
    public function escrowBank(): ?string
    {
        return $this->result['EscrowBank'];
    }

    /**
     * 信用卡支付回傳（一次付清、Google Pay、Samaung Pay、國民旅遊卡、銀聯）
     */
    public function credit(): CreditResult
    {
        return new CreditResult($this->result);
    }

    /**
     * WEBATM、ATM 繳費回傳
     */
    public function atm(): ATMResult
    {
        return new ATMResult($this->result);
    }

    /**
     * 超商代碼繳費回傳
     */
    public function storeCode(): StoreCodeResult
    {
        return new StoreCodeResult($this->result);
    }

    /**
     * 超商條碼繳費回傳
     */
    public function storeBarcode(): StoreBarcodeResult
    {
        return new StoreBarcodeResult($this->result);
    }

    /**
     * 超商物流回傳
     */
    public function lgs(): LgsResult
    {
        return new LgsResult($this->result);
    }

    /**
     * 跨境支付回傳 (包含簡單付電子錢包、簡單付微信支付、簡單付支付寶)
     */
    public function ezPay(): EzPayResult
    {
        return new EzPayResult($this->result);
    }

    /**
     * 玉山 Wallet 回傳
     */
    public function esunWallet(): EsunWalletResult
    {
        return new EsunWalletResult($this->result);
    }

    /**
     * 台灣 Pay 回傳
     */
    public function taiwanPay(): TaiwanPayResult
    {
        return new TaiwanPayResult($this->result);
    }

    /**
     * Transform the result data.
     */
    protected function transformResult(array $data): array
    {
        return $data['TradeInfo']['Result'] ?? [];
    }
}
