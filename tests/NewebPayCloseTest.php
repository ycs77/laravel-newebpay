<?php

namespace Ycs77\NewebPay\Test;

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayClose;
use Ycs77\NewebPay\Sender\Async;

class NewebPayCloseTest extends TestCase
{
    public function test_neweb_pay_close_get_url()
    {
        $newebpay = new NewebPayClose($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/API/CreditCard/Close', $newebpay->getUrl());
    }

    public function test_neweb_pay_close_sender_is_sync()
    {
        $newebpay = new NewebPayClose($this->createMockConfig());

        $this->assertInstanceOf(Async::class, $newebpay->getSender());
    }

    public function test_neweb_pay_close_get_request_data()
    {
        $this->setTestNow();

        $newebpay = new NewebPayClose($this->createMockConfig());

        $requestData = $newebpay->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID_']);
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b6c2ad3875eb127ca33809ddd77de94550', $requestData['PostData_']);
    }

    public function test_neweb_pay_close_submit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayClose($this->createMockConfig());

        $result = $newebpay
            ->setCloseOrder('TestNo123456', 100, 'order')
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
