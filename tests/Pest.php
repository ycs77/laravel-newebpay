<?php

use Illuminate\Support\Carbon;
use Ycs77\NewebPay\Tests\EncryptTradeData;

if (function_exists('pest')) {
    pest()->extend(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
    pest()->extend(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');
} else {
    // Fallback for Pest v1.x
    uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Feature');
    uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');
}

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
