<?php

// Architecture testing is available in PestPHP v2.0+
if (function_exists('arch')) {
    arch('main')
        ->expect('Ycs77\NewebPay')
        ->not->toUse(['die', 'dd', 'dump'])
        ->ignoring('Ycs77\NewebPay\Builders\Concerns\Dumpable');

    arch('attributes')
        ->expect('Ycs77\NewebPay\Attributes')
        ->toUseNothing();

    $buildersIgnored = [
        'Ycs77\NewebPay\Builders\Builder',
        'Ycs77\NewebPay\Builders\Concerns',
    ];

    $buildersWithoutResource = [
        ...$buildersIgnored,
        'Ycs77\NewebPay\Builders\Trade\MPGBuilder',
        'Ycs77\NewebPay\Builders\Period\CreateBuilder',
    ];

    $buildersNotFinal = [
        ...$buildersIgnored,
        'Ycs77\NewebPay\Builders\Trade\QueryBuilder',
    ];

    // 注意：`ignoring()` 方法目前只會對上一行定義的期望有效
    // 因此需要對每個期望都呼叫一次 `ignoring()` 方法
    arch('builders')
        ->expect('Ycs77\NewebPay\Builders')
        ->toBeFinal()
        ->ignoring($buildersNotFinal)
        ->toHaveAttribute('Ycs77\NewebPay\Attributes\Resource')
        ->ignoring($buildersWithoutResource);

    arch('contracts')
        ->expect('Ycs77\NewebPay\Contracts')
        ->toBeInterfaces();

    arch('enums')
        ->expect('Ycs77\NewebPay\Enums')
        ->toBeEnums();

    arch('exceptions')
        ->expect('Ycs77\NewebPay\Exceptions')
        ->toUseNothing();

    arch('facades')
        ->expect('Ycs77\NewebPay\Facades')
        ->toOnlyUse([
            'Illuminate\Support\Facades\Facade',
        ]);

    arch('options')
        ->expect('Ycs77\NewebPay\Options')
        ->toBeFinal()
        ->ignoring('Ycs77\NewebPay\Options\Options');

    arch('resources')
        ->expect('Ycs77\NewebPay\Resources')
        ->toBeFinal();
}
