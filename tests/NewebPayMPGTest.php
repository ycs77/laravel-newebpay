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
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734fc3682f79cdaba7e8489a37de705b82690f6a3fb4d3fcc40c7e8e768a27ac31efda27631339d3a015b05bd4a9bfec80a56f5fd241871b7784d13e6292c9509a2b5ea4e79da503ff3aae93158b88ee0178af3f2e47a6a7ece18b11bdea3489a45b652543a9389e35a97d159422e163e825e099ff419da4eaab9f2348bfd445c998cc7f48fdf577151d6acc2057382d0b1f7080ec1bf0bff3454187b70d6a1172069d1cd828d40d039707e132fe4811b3e586a4f534ebdcad9122f3588c52c13981802ce9a013e0082246a38055f68957259f9605fcd0e59d461a7e32b472509a416058fbb27706bfa6d5571e0352c10a678dd3a51501e09c0c555810a0a3d560e', $requestData['TradeInfo']);
        $this->assertEquals('A494164B0A116B598B5F6C654144249B355434870F6CFFF0C8C8E5504AA82DBF', $requestData['TradeSha']);
        $this->assertEquals('1.5', $requestData['Version']);
    }

    public function testNewebPayMPGSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayMPG($this->createMockConfig());

        $result = $newebpay
            ->setOrder('TestNo123456', 100, '測試商品', 'test@email.com')
            ->submit();

        $this->assertEquals('<form id="order-form" method="post" action=https://ccore.newebpay.com/MPG/mpg_gateway ><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734fc3682f79cdaba7e8489a37de705b82690f6a3fb4d3fcc40c7e8e768a27ac31efda27631339d3a015b05bd4a9bfec80a56f5fd241871b7784d13e6292c9509a2b5ea4e79da503ff3aae93158b88ee0178af3f2e47a6a7ece18b11bdea3489a45b652543a9389e35a97d159422e163e825e099ff419da4eaab9f2348bfd445c998cc7f48fdf577151d6acc2057382d0b1f7080ec1bf0bff3454187b70d6a1172069d1cd828d40d039707e132fe4811b3e586a4f534ebdcad9122f3588c52c13981802ce9a013e0082246a38055f68957259f9605fcd0e59d461a7e32b472509a4ed8dcd76df5c4731642a8788ad4a5c3e118260858f1d11a432bc27ca32f437c403d698fcda93f35f063e3bf349d16836f884b9c0316e02872187b76943a72d5cc09c4fae8bf205fcf1c78e176448d56cf41bfedac0bb8ed992fc362487aec55c29bf7d48409c08e4a492d9ff34af72c73597f95b22c00a2bc7d932caa8a97ab4"><input type="hidden" name="TradeSha" value="53A5834A77B27F6AAA99A8AD76EFCF59286D89374FAE6CD804A2611EB23B8677"><input type="hidden" name="Version" value="1.5"></form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>', $result);
    }
}
