<?php

namespace Ycs77\NewebPay\Test;

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayQuery;
use Ycs77\NewebPay\Sender\Async;

class NewebPayQueryTest extends TestCase
{
    public function testNewebPayQueryGetUrl()
    {
        $newebpay = new NewebPayQuery($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/API/QueryTradeInfo', $newebpay->getUrl());
    }

    public function testNewebPayQuerySenderIsSync()
    {
        $newebpay = new NewebPayQuery($this->createMockConfig());

        $this->assertInstanceOf(Async::class, $newebpay->getSender());
    }

    public function testNewebPayQueryGetRequestData()
    {
        $this->setTestNow();

        $newebpay = new NewebPayQuery($this->createMockConfig());

        $requestData = $newebpay
            ->setQuery('TestNo123456', 100)
            ->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID']);
        $this->assertEquals('1.5', $requestData['Version']);
        $this->assertEquals('json', $requestData['RespondType']);
        $this->assertEquals('A314C865681049301D80A33318E5043B51425EAC58736E9ACF4FAC5854ABD59F', $requestData['CheckValue']);
        $this->assertEquals(1577833200, $requestData['TimeStamp']);
        $this->assertEquals('TestNo123456', $requestData['MerchantOrderNo']);
        $this->assertEquals(100, $requestData['Amt']);
    }

    public function testNewebPayQuerySubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayQuery($this->createMockConfig());

        $result = $newebpay
            ->setQuery('TestNo123456', 100)
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
