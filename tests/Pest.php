<?php

use Illuminate\Support\Carbon;
use Mockery as m;
use Ycs77\LaravelRecoverSession\UserSource;

uses(Ycs77\NewebPay\Tests\TestCase::class)->in('Unit');

function setTestNow()
{
    Carbon::setTestNow(Carbon::create(2020, 1, 1));
}

function fakeUserSource()
{
    /** @var \Ycs77\LaravelRecoverSession\UserSource|\Mockery\MockInterface|\Mockery\LegacyMockInterface */
    $userSource = m::mock(UserSource::class);
    $userSource->shouldReceive('preserve');

    app()->instance(UserSource::class, $userSource);

    return $userSource;
}
