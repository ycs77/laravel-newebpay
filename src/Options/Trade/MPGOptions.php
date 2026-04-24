<?php

namespace Ycs77\NewebPay\Options\Trade;

use Carbon\Carbon;
use Ycs77\NewebPay\Enums\Bank;
use Ycs77\NewebPay\Enums\CreditInst;
use Ycs77\NewebPay\Enums\CreditRememberDemand;
use Ycs77\NewebPay\Enums\CVSCOM;
use Ycs77\NewebPay\Enums\LangType;
use Ycs77\NewebPay\Enums\LgsType;
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

    public array $paymentMethods = [];

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
        if ($this->paymentMethods['credit']['enabled']) {
            $tradeData['CREDIT'] = 1;

            if ($this->paymentMethods['credit']['red']) {
                $tradeData['CreditRed'] = 1;
            }

            if (($this->paymentMethods['credit']['inst'] instanceof CreditInst &&
                $this->paymentMethods['credit']['inst'] !== CreditInst::NONE) ||
                is_array($this->paymentMethods['credit']['inst'])
            ) {
                $tradeData['InstFlag'] = collect($this->paymentMethods['credit']['inst'])
                    ->map(fn (CreditInst $inst) => $inst->value)
                    ->join(',');
            } elseif (is_numeric($this->paymentMethods['credit']['inst']) || is_string($this->paymentMethods['credit']['inst'])) {
                $tradeData['InstFlag'] = $this->paymentMethods['credit']['inst'];
            }
        }
        if ($this->paymentMethods['webATM']) {
            $tradeData['WEBATM'] = 1;
        }
        if ($this->paymentMethods['VACC']) {
            $tradeData['VACC'] = 1;
        }
        if ($this->paymentMethods['bank'] instanceof Bank &&
            $this->paymentMethods['bank'] !== Bank::ALL ||
            is_array($this->paymentMethods['bank'])
        ) {
            $tradeData['BankType'] = collect($this->paymentMethods['bank'])
                ->map(fn (Bank $inst) => $inst->value)
                ->join(',');
        } elseif (is_string($this->paymentMethods['bank'])) {
            $tradeData['BankType'] = $this->paymentMethods['bank'];
        }
        if ($this->paymentMethods['NTCB']['enabled']) {
            $tradeData['NTCB'] = 1;
            /** @see \Ycs77\NewebPay\Enums\NTCBLocate */
            $tradeData['NTCBLocate'] = $this->paymentMethods['NTCB']['locate']->value;
            $tradeData['NTCBStartDate'] = $this->paymentMethods['NTCB']['start_date'];
            $tradeData['NTCBEndDate'] = $this->paymentMethods['NTCB']['end_date'];
        }

        // 其他支付方式
        if ($this->paymentMethods['googlePay']) {
            $tradeData['ANDROIDPAY'] = 1;
        }
        if ($this->paymentMethods['samsungPay']) {
            $tradeData['SAMSUNGPAY'] = 1;
        }
        if ((is_array($this->paymentMethods['linePay']) &&
            $this->paymentMethods['linePay']['enabled']) ||
            $this->paymentMethods['linePay'] === true
        ) {
            $tradeData['LINEPAY'] = 1;
            if (isset($this->paymentMethods['linePay']['image_url'])) {
                $tradeData['ImageUrl'] = $this->paymentMethods['linePay']['image_url'];
            }
        }
        if ($this->paymentMethods['unionPay']) {
            $tradeData['UNIONPAY'] = 1;
        }
        if ($this->paymentMethods['esunWallet']) {
            $tradeData['ESUNWALLET'] = 1;
        }
        if ($this->paymentMethods['taiwanPay']) {
            $tradeData['TAIWANPAY'] = 1;
        }
        if ($this->paymentMethods['ezPay']) {
            $tradeData['EZPAY'] = 1;
        }
        if ($this->paymentMethods['ezpWeChat']) {
            $tradeData['EZPWECHAT'] = 1;
        }
        if ($this->paymentMethods['ezpAlipay']) {
            $tradeData['EZPALIPAY'] = 1;
        }

        // 超商代碼繳費與條碼繳費
        if ($this->paymentMethods['CVS']) {
            $tradeData['CVS'] = 1;
        }
        if ($this->paymentMethods['barcode']) {
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
