<?php

namespace Ycs77\NewebPay\Test;

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayClose;
use Ycs77\NewebPay\Sender\Async;

class NewebPayCloseTest extends TestCase
{
    public function testNewebPayCloseGetUrl()
    {
        $newebpay = new NewebPayClose($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/API/CreditCard/Close', $newebpay->getUrl());
    }

    public function testNewebPayCloseSenderIsSync()
    {
        $newebpay = new NewebPayClose($this->createMockConfig());

        $this->assertInstanceOf(Async::class, $newebpay->getSender());
    }

    public function testNewebPayCloseGetRequestData()
    {
        $this->setTestNow();

        $newebpay = new NewebPayClose($this->createMockConfig());

        $requestData = $newebpay->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID_']);
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734fc3682f79cdaba7e8489a37de705b826f131fb14552b6a862298c314b869888b', $requestData['PostData_']);
    }

    public function testNewebPayCloseSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayClose($this->createMockConfig());

        $result = $newebpay
            ->setCloseOrder('TestNo123456', 100, 'order')
            ->setMockHttp([
                new Response(200, [], '{"Status":"Code001","Message":"Test message.","Result":[]}')
            ])
            ->submit();

        $this->assertEquals([
            'Status' => 'Code001',
            'Message' => 'Test message.',
            'Result' => [],
        ], $result);
    }
}
