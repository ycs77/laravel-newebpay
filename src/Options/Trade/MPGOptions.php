<?php

namespace Ycs77\NewebPay\Options\Trade;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
use Ycs77\NewebPay\Enums\NTCBLocate;
use Ycs77\NewebPay\Options\Options;

final class MPGOptions extends Options
{
    public string $merchantId = '';

    public string $version = '2.0';

    public ?LangType $lang = null;

    public string $orderNo = '';

    public int $amount = 0;

    public string $itemDescription = '';

    public ?int $tradeLimit = null;

    public ?string $expireDate = null;

    public ?string $returnURL = null;

    public ?string $notifyURL = null;

    public ?string $customerURL = null;

    public ?string $clientBackURL = null;

    public ?string $email = null;

    public ?bool $emailModify = null;

    public ?string $orderComment = null;

    public bool $credit = false;

    public bool $creditRed = false;

    public CreditInst|array $creditInstallment = CreditInst::NONE;

    public bool $webAtm = false;

    public bool $atmTransfer = false;

    public Bank|array|null $bank = null;

    public bool $nationalTravelCard = false;

    public ?NTCBLocate $nationalTravelCardLocate = null;

    public ?string $nationalTravelCardStartDate = null;

    public ?string $nationalTravelCardEndDate = null;

    public bool $googlePay = false;

    public bool $samsungPay = false;

    public bool $linePay = false;

    public ?string $linePayImageUrl = null;

    public bool $unionPay = false;

    public bool $esunWallet = false;

    public bool $taiwanPay = false;

    public bool $ezPay = false;

    public bool $ezPayWeChat = false;

    public bool $ezPayAlipay = false;

    public bool $cvsCode = false;

    public bool $barcode = false;

    public ?string $creditRememberIdentifier = null;

    public ?CreditRememberDemand $creditRememberDemand = null;

    public ?CVSCOM $cvscom = null;

    public ?LgsType $lgsType = null;

    public function toArray()
    {
        $tradeData = [
            'MerchantID' => $this->merchantId,
            'RespondType' => 'JSON',
            'TimeStamp' => Carbon::now()->timestamp,
            'Version' => $this->version,
            'LangType' => $this->lang?->value,
            'MerchantOrderNo' => $this->orderNo,
            'Amt' => $this->amount,
            'ItemDesc' => $this->itemDescription,
            'TradeLimit' => $this->tradeLimit,
            'ExpireDate' => $this->expireDate,
            'ReturnURL' => $this->returnURL,
            'NotifyURL' => $this->notifyURL,
            'CustomerURL' => $this->customerURL,
            'ClientBackURL' => $this->clientBackURL,
            'Email' => $this->email,
            'EmailModify' => $this->emailModify === false ? 0 : null,
            'OrderComment' => $this->orderComment,
            'TokenTerm' => $this->creditRememberIdentifier,
            'TokenTermDemand' => $this->creditRememberDemand?->value,
            'CVSCOM' => $this->cvscom?->value,
            'LgsType' => $this->lgsType?->value,
        ];

        // 銀行相關支付方式
        if ($this->credit) {
            $tradeData['CREDIT'] = 1;

            if ($this->creditRed) {
                $tradeData['CreditRed'] = 1;
            }

            if (($this->creditInstallment instanceof CreditInst &&
                $this->creditInstallment !== CreditInst::NONE) ||
                is_array($this->creditInstallment)
            ) {
                $tradeData['InstFlag'] = collect($this->creditInstallment)
                    ->map(fn (CreditInst $inst) => $inst->value)
                    ->join(',');
            }
        }
        if ($this->webAtm) {
            $tradeData['WEBATM'] = 1;
        }
        if ($this->atmTransfer) {
            $tradeData['VACC'] = 1;
        }
        if ($this->bank instanceof Bank &&
            $this->bank !== Bank::ALL ||
            is_array($this->bank)
        ) {
            $tradeData['BankType'] = collect($this->bank)
                ->map(fn (Bank $inst) => $inst->value)
                ->join(',');
        }
        if ($this->nationalTravelCard) {
            $tradeData['NTCB'] = 1;
            $tradeData['NTCBLocate'] = $this->nationalTravelCardLocate?->value;
            $tradeData['NTCBStartDate'] = $this->nationalTravelCardStartDate;
            $tradeData['NTCBEndDate'] = $this->nationalTravelCardEndDate;
        }

        // 其他支付方式
        if ($this->googlePay) {
            $tradeData['ANDROIDPAY'] = 1;
        }
        if ($this->samsungPay) {
            $tradeData['SAMSUNGPAY'] = 1;
        }
        if ($this->linePay) {
            $tradeData['LINEPAY'] = 1;
            if ($this->linePayImageUrl !== null) {
                $tradeData['ImageUrl'] = $this->linePayImageUrl;
            }
        }
        if ($this->unionPay) {
            $tradeData['UNIONPAY'] = 1;
        }
        if ($this->esunWallet) {
            $tradeData['ESUNWALLET'] = 1;
        }
        if ($this->taiwanPay) {
            $tradeData['TAIWANPAY'] = 1;
        }
        if ($this->ezPay) {
            $tradeData['EZPAY'] = 1;
        }
        if ($this->ezPayWeChat) {
            $tradeData['EZPWECHAT'] = 1;
        }
        if ($this->ezPayAlipay) {
            $tradeData['EZPALIPAY'] = 1;
        }

        // 超商代碼繳費與條碼繳費
        if ($this->cvsCode) {
            $tradeData['CVS'] = 1;
        }
        if ($this->barcode) {
            $tradeData['BARCODE'] = 1;
        }

        return [
            'MerchantID' => $this->merchantId,
            'TradeInfo' => array_filter($tradeData, fn ($value) => ! is_null($value)),
            'TradeSha' => '',
            'Version' => $this->version,
        ];
    }
}
