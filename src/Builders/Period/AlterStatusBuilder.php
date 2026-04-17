<?php

namespace Ycs77\NewebPay\Builders\Period;

use Ycs77\NewebPay\Attributes\Resource;
use Ycs77\NewebPay\Builders\Builder;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Exceptions\NewebPayException;
use Ycs77\NewebPay\Options\Period\AlterStatusOptions;
use Ycs77\NewebPay\Resources\Period as PeriodResource;
use Ycs77\NewebPay\Results\Period\AlterStatusResult;

#[Resource(PeriodResource::class, 'alterStatus')]
final class AlterStatusBuilder extends Builder
{
    private AlterStatusOptions $options;

    protected function boot(): void
    {
        $this->crypto->setHashKey($this->config['hash_key']);
        $this->crypto->setHashIv($this->config['hash_iv']);

        $this->options = new AlterStatusOptions;
        $this->options->merchantId = $this->config['merchant_id'];

        $this->endpoint = '/MPG/period/AlterStatus';
    }

    public function options(): AlterStatusOptions
    {
        return $this->options;
    }

    /**
     * 商店訂單編號
     */
    public function withOrder(string $orderNo): self
    {
        $this->options->orderNo = $orderNo;

        return $this;
    }

    /**
     * 委託單號
     */
    public function withPeriod(string $periodNo): self
    {
        $this->options->periodNo = $periodNo;

        return $this;
    }

    /**
     * 委託狀態
     */
    public function withStatus(PeriodStatus $status): self
    {
        $this->options->alterType = $status;

        return $this;
    }

    /**
     * 暫停委託
     */
    public function suspend(): AlterStatusResult
    {
        return $this
            ->withStatus(PeriodStatus::SUSPEND)
            ->send();
    }

    /**
     * 終止委託
     */
    public function terminate(): AlterStatusResult
    {
        return $this
            ->withStatus(PeriodStatus::TERMINATE)
            ->send();
    }

    /**
     * 恢復委託
     */
    public function resume(): AlterStatusResult
    {
        return $this
            ->withStatus(PeriodStatus::RESTART)
            ->send();
    }

    /**
     * 送出修改委託狀態請求
     *
     * @throws NewebPayException
     */
    public function send(): AlterStatusResult
    {
        if ($result = $this->record()) {
            return $result;
        }

        $requestData = $this->toRequestData();

        $data = $this->sendRequest($requestData);
        $data['period'] = $this->crypto->decryptByAES($data['period']);

        $status = $data['period']['Status'];
        $message = $data['period']['Message'];

        if ($status !== 'SUCCESS') {
            throw new NewebPayException(
                $status, $message, $requestData['url'], $requestData['formData']
            );
        }

        return new AlterStatusResult($data);
    }
}
