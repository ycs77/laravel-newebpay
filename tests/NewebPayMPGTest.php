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
        $this->assertEquals('e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734278f598f155ed8332ac407ba9008404d0da5cc3d40e3fe5102c8800ee55c53392a0ea77b0915a4cfd62bbacfc45e94fb8721f5152098d7ce5e9cb82b90cda8963116a593eca0edf44e274ad5a2ea84eef6dd0043a07065c41d7c6e4ce7009d8993862088130cbf19dbe08c5e1ac0df5b15bedbdbd9ebe215d172fe620a4c60ebd5c4e1e0809289152ea47e27ef52c3770e14ed699197604960cfe748d47a06f1771732e0e60add841e944e9da533a0575b48d931bb5d2f668c00a30d418deb23f26cd4a2a3604333c01b5b1e0a90cb99233a162e5348f75ec0f95e86ea1973bc97c4c5ec0d253cc1e6b9318174d32f64361c3efedda8092c84dd06719e7e140f', $requestData['TradeInfo']);
        $this->assertEquals('01D7A4D5BA76CBE0134C27BC89C402249D399A31373AE94DF1127436793626D3', $requestData['TradeSha']);
        $this->assertEquals('1.5', $requestData['Version']);
    }

    public function testNewebPayMPGSubmit()
    {
        $this->setTestNow();

        $newebpay = new NewebPayMPG($this->createMockConfig());

        $result = $newebpay
            ->setOrder('TestNo123456', 100, '測試商品', 'test@email.com')
            ->submit();

        $this->assertEquals('<form id="order-form" method="post" action=https://ccore.newebpay.com/MPG/mpg_gateway ><input type="hidden" name="MerchantID" value="TestMerchantID1234"><input type="hidden" name="TradeInfo" value="e88e33cc07d106bcba1c1bd02d5d421f29e99fa551a1062eb25c9eb769877734278f598f155ed8332ac407ba9008404d0da5cc3d40e3fe5102c8800ee55c53392a0ea77b0915a4cfd62bbacfc45e94fb8721f5152098d7ce5e9cb82b90cda8963116a593eca0edf44e274ad5a2ea84eef6dd0043a07065c41d7c6e4ce7009d8993862088130cbf19dbe08c5e1ac0df5b15bedbdbd9ebe215d172fe620a4c60ebd5c4e1e0809289152ea47e27ef52c3770e14ed699197604960cfe748d47a06f1771732e0e60add841e944e9da533a0575b48d931bb5d2f668c00a30d418deb23f26cd4a2a3604333c01b5b1e0a90cb99233a162e5348f75ec0f95e86ea1973bc3e3a73cb840d689fde9ada990f5e4a515dbd9125317cc04873c6b860c098b7cd122fe00d38505c37954db6c533cebac7bb322f841479a0e90728805d469ccfad1862bf42feb2cdff1b9aaab40dde79896bd3bd0573674cccb089bbdbab30b20984fcf8da97592cad0429c5cc1035bbae0eed4eb30ed70dd5320c9c410f5c0f38"><input type="hidden" name="TradeSha" value="981C58EA56676114B0DA048C0BD86785F5C86B6C5FF40AA5CC58D898A92FEE60"><input type="hidden" name="Version" value="1.5"></form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>', $result);
    }
}
