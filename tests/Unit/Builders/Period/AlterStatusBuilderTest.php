<?php

use Carbon\Carbon;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Ycs77\NewebPay\Builders\Period\AlterStatusBuilder;
use Ycs77\NewebPay\Contracts\HttpTransporter;
use Ycs77\NewebPay\Crypto\Crypto;
use Ycs77\NewebPay\Enums\PeriodStatus;
use Ycs77\NewebPay\Factory;
use Ycs77\NewebPay\Options\Options;
use Ycs77\NewebPay\Results\Period\AlterStatusResult;

beforeEach(function () {
    $this->factory = app(Factory::class);

    $this->crypto = mock(Crypto::class);
    $this->crypto->allows('setHashKey');
    $this->crypto->allows('setHashIv');
    $this->crypto->allows('encryptByAES')->andReturn('encrypted_data');

    $this->response = mock(HttpClientResponse::class);
    $this->response->allows('json')->andReturn([
        // TODO: 暫時 pass 測試，之後要重構
        'Status' => 'SUCCESS',
        'period' => [
            'Status' => 'SUCCESS',
        ],
    ]);

    $this->httpTransporter = mock(HttpTransporter::class);
    $this->httpTransporter->allows('setTimeout');
    $this->httpTransporter->allows('send')->andReturn($this->response);
});

test('可以成功修改信用卡定期定額委託狀態', function () {
    $expectedOptionsData = [
        'MerchantID_' => 'TestMerchantID1234',
        'PostData_' => [
            'RespondType' => 'JSON',
            'Version' => '1.0',
            'TimeStamp' => Carbon::now()->timestamp,
            'MerOrderNo' => 'Order001',
            'PeriodNo' => '20200101000000001',
            'AlterType' => 'terminate',
        ],
    ];

    $result = (new AlterStatusBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withStatus(PeriodStatus::TERMINATE)
        ->onPreparedOptions(function (Options $options) use ($expectedOptionsData) {
            expect($options->toArray())->toBe($expectedOptionsData);
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterStatusResult::class);
});

test('可以暫停信用卡定期定額委託', function () {
    $result = (new AlterStatusBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withStatus(PeriodStatus::SUSPEND)
        ->onPreparedOptions(function (Options $options) {
            expect($options->toArray()['PostData_']['AlterType'])->toBe('suspend');
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterStatusResult::class);
});

test('可以重啟信用卡定期定額委託', function () {
    $result = (new AlterStatusBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withStatus(PeriodStatus::RESTART)
        ->onPreparedOptions(function (Options $options) {
            expect($options->toArray()['PostData_']['AlterType'])->toBe('restart');
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterStatusResult::class);
});

test('可以終止信用卡定期定額委託', function () {
    $result = (new AlterStatusBuilder($this->factory, $this->crypto, $this->httpTransporter))
        ->withOrder('Order001')
        ->withPeriod('20200101000000001')
        ->withStatus(PeriodStatus::TERMINATE)
        ->onPreparedOptions(function (Options $options) {
            expect($options->toArray()['PostData_']['AlterType'])->toBe('terminate');
        })
        ->send();

    expect($result)->toBeInstanceOf(AlterStatusResult::class);
});
