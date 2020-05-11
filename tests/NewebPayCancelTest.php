<?php

namespace Ycs77\NewebPay\Test;

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCancel;
use Ycs77\NewebPay\Sender\Async;

class NewebPayCancelTest extends TestCase
{
    public function testNewebPayCancelGetUrl()
    {
        $newebpay = new NewebPayCancel($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/API/CreditCard/Cancel', $newebpay->getUrl());
    }

    public function testNewebPayCancelSenderIsSync()
    {
        $newebpay = new NewebPayCancel($this->createMockConfig());

        $this->assertInstanceOf(Async::class, $newebpay->getSender());
    }

    public function testNewebPayCancelGetRequestData()
    {
        $this->setTestNow();

        $newebpay = new NewebPayCancel($this->createMockConfig());

        $requestData = $newebpay->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID_']);
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734278f598f155ed8332ac407ba9008404d1f5cee9d189ee7542d3952ca0553167c', $requestData['PostData_']);
    }

    public function testNewebPayCancelSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayCancel($this->createMockConfig());

        $result = $newebpay
            ->setCancelOrder('TestNo123456', 100, 'order')
            ->setMockHttp([
                new Response(200, [], '{"Status":"Code001","Message":"Test message.","Result":[]}'),
            ])
            ->submit();

        $this->assertEquals([
            'Status' => 'Code001',
            'Message' => 'Test message.',
            'Result' => [],
        ], $result);
    }
}
