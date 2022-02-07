<?php

namespace Ycs77\NewebPay\Test;

use Ycs77\NewebPay\NewebPayMPG;
use Ycs77\NewebPay\Sender\Sync;

class NewebPayMPGTest extends TestCase
{
    public function testNewebPayMPGGetUrl()
    {
        $newebpay = new NewebPayMPG($this->createMockConfig());

        $this->assertEquals('https://ccore.newebpay.com/MPG/mpg_gateway', $newebpay->getUrl());
    }

    public function testNewebPayMPGSenderIsSync()
    {
        $newebpay = new NewebPayMPG($this->createMockConfig());

        $this->assertInstanceOf(Sync::class, $newebpay->getSender());
    }

    public function testNewebPayMPGGetRequestData()
    {
        $this->setTestNow();

        $newebpay = new NewebPayMPG($this->createMockConfig());

        $requestData = $newebpay->getRequestData();

        $this->assertEquals('TestMerchantID1234', $requestData['MerchantID']);
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b633b2b337c5dc2f6001d3f14dcb80df6cfc4ffe7a624838789bc47fcdd438db49a5f3e2b48d1740160d307a1bf6f27190b8825723f59d0cdf4071229db0a7bb6b2ef12ce7be24b0467db60a4185908770e1b5238444fb00fa24bb7693f9fb8d8ca5e4eccd795b96eba3bbcda6a10e3a708b41876412e14972975e9bc372e3691c51b9c1cdc95b5eeece597d619ea056dff7f2981caf893110355dcac863c3f54b8fa2c3c555f7e2fbf512246d226b1cffd84499807cbe755768b3bac5d77ab361b69a468eb2346b5ba380cc664609e3f58890893dcecd5cbb371033451a19253b206604b376e7b402ab92369a16e63f87', $requestData['TradeInfo']);
        $this->assertEquals('519E6394DADAB09451A753E845C782EFC34EE8C1D0B41092E4CF220A8112C418', $requestData['TradeSha']);
        $this->assertEquals('1.5', $requestData['Version']);
    }

    public function testNewebPayMPGSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayMPG($this->createMockConfig());

        $result = $newebpay
            ->setOrder('TestNo123456', 100, '測試商品', 'test@email.com')
            ->submit();

        $this->assertEquals('<form id="order-form" method="post" action=https://ccore.newebpay.com/MPG/mpg_gateway ><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="e88e33cc07d106bcba1c1bd02d5d421fa9f86ef5a1469c0e801b3813b360f8333fd9fef8bf7312a3e5e66e1f6b5601b633b2b337c5dc2f6001d3f14dcb80df6cfc4ffe7a624838789bc47fcdd438db49a5f3e2b48d1740160d307a1bf6f27190b8825723f59d0cdf4071229db0a7bb6b2ef12ce7be24b0467db60a4185908770e1b5238444fb00fa24bb7693f9fb8d8ca5e4eccd795b96eba3bbcda6a10e3a708b41876412e14972975e9bc372e3691c51b9c1cdc95b5eeece597d619ea056dff7f2981caf893110355dcac863c3f54b8fa2c3c555f7e2fbf512246d226b1cffd84499807cbe755768b3bac5d77ab361b69a468eb2346b5ba380cc664609e3f5d3972ecc3f47822c86e16d206ef609224042948fc2f916fcba8e2182a3ef3144078df4c145889c0248e363a59910f33a1363a919f9a3b4bce2f5500c1268ccb6dd4e0161072eb96d3dc8e78da3a38ec1c2d02d23371554115c31463044a6c26b16c610b970284d60b8864ae3bb025111bc617218ebdf3b276b903c69c65762f9"><input type="hidden" name="TradeSha" value="16E9537DFCE60BE89B3260E7B6A8E59AD79676ED69DDF4D156D5E49A89C3A0C3"><input type="hidden" name="Version" value="1.5"></form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>', $result);
    }
}
