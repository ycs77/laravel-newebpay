<?php

use Illuminate\Support\Carbon;

uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');

function setTestNow()
{
    Carbon::setTestNow(Carbon::create(2020, 1, 1));
}
