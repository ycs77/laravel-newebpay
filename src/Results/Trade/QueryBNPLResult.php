<?php

namespace Ycs77\NewebPay\Results\Trade;

use Ycs77\NewebPay\Enums\BNPLCaptureStatus;
use Ycs77\NewebPay\Enums\BNPLRefundStatus;
use Ycs77\NewebPay\Enums\PaymentType;
use Ycs77\NewebPay\Results\Result;

class QueryBNPLResult extends Result
{
    /**
     * 交易類別中英文名稱對照
     */
    protected $paymentMethods = [
        'AFTEE' => 'AFTEE 先享後付',
        'OPPAY' => '大哥付你分期',
    ];

    /**
     * 金融機構回應碼
     */
    public function respondCode(): ?string
    {
        return $this->data['RespondCode'] ?? null;
    }

    /**
     * 回應結果訊息
     *
     * BNPL 上游回覆此次交易結果狀態
     */
    public function respondMsg(): string
    {
        return $this->data['RespondMsg'];
    }

    /**
     * 請款金額
     */
    public function closeAmt(): ?int
    {
        return $this->data['CloseAmt'] ?? null;
    }

    /**
     * 請款狀態
     */
    public function closeStatus(): BNPLCaptureStatus
    {
        return BNPLCaptureStatus::from((int) $this->data['CloseStatus']);
    }

    /**
     * 可退款餘額
     *
     * 1. 若此筆交易未發動退款，則本欄位回傳值為可退款金額
     * 2. 若此筆交易已發動退款，則本欄位回傳值為可退款餘額
     */
    public function backBalance(): int
    {
        return $this->data['BackBalance'];
    }

    /**
     * 退款狀態
     */
    public function backStatus(): BNPLRefundStatus
    {
        return BNPLRefundStatus::from((int) $this->data['BackStatus']);
    }

    /**
     * 分期交易期別
     *
     * 支付工具啟用值帶 1 時固定為 0；帶 2 時帶消費者實際選擇之期別，一次付清為 0。
     */
    public function inst(): int
    {
        return $this->data['Inst'];
    }

    /**
     * 交易類別
     *
     * * **AFTEE**: AFTEE 先享後付
     * * **OPPAY**: 大哥付你分期
     */
    public function paymentMethod(): PaymentType
    {
        return PaymentType::from($this->data['PaymentMethod']);
    }

    /**
     * 交易類別中文名稱
     */
    public function paymentMethodName(): string
    {
        return $this->paymentMethods[$this->data['PaymentMethod']] ?? $this->data['PaymentMethod'];
    }

    /**
     * Define the allowed data keys.
     */
    protected function allowedDataKeys(): array
    {
        return [
            'RespondCode',
            'RespondMsg',
            'CloseAmt',
            'CloseStatus',
            'BackBalance',
            'BackStatus',
            'Inst',
            'PaymentMethod',
        ];
    }
}
