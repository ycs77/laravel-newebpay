<?php

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withPreparedSets(
        deadCode: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withSkip([
        ClosureToArrowFunctionRector::class,
    ]);
