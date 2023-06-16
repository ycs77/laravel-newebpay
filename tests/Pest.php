<?php

use Illuminate\Support\Carbon;
use Ycs77\NewebPay\Tests\EncryptTradeData;

uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');

function setTestNow()
{
    Carbon::setTestNow(Carbon::create(2020, 1, 1));
}

function encryptTradeData(array $tradeData)
{
    $newebPay = new EncryptTradeData(app('config'), app('session.store'));

    return $newebPay
        ->setTradeData($tradeData)
        ->encryptData();
}
