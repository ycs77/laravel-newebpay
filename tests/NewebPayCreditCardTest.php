<?php

namespace Ycs77\NewebPay\Test;

use GuzzleHttp\Psr7\Response;
use Ycs77\NewebPay\NewebPayCreditCard;
use Ycs77\NewebPay\Sender\Async;

class NewebPayCreditCardTest extends TestCase
{
    public function testNewebPayCreditCardGetUrl()
    {
        $newebpay = new NewebPayCreditCard($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/API/CreditCard', $newebpay->getUrl());
    }

    public function testNewebPayCreditCardSenderIsSync()
    {
        $newebpay = new NewebPayCreditCard($this->createMockConfig());

        $this->assertInstanceOf(Async::class, $newebpay->getSender());
    }

    public function testNewebPayCreditCardGetRequestData()
    {
        $this->setTestNow();

        $newebpay = new NewebPayCreditCard($this->createMockConfig());

        $requestData = $newebpay->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID_']);
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734278f598f155ed8332ac407ba9008404da2575732d4e6053041aae2eda4736163', $requestData['PostData_']);
        $this->assertEquals('JSON', $requestData['Pos_']);
    }

    public function testNewebPayCreditCardSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayCreditCard($this->createMockConfig());

        $result = $newebpay
            ->firstTrade([
                'no' => 'TestNo123456',
                'amt' => 100,
                'desc' => '測試商品',
                'email' => 'test@email.com',
                'cardNo' => '0000-0000-0000-0000',
                'exp' => '',
                'cvc' => '',
                'tokenTerm' => '',
            ])
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
