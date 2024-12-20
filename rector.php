<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withImportNames(false)
    ->withParallel()
    ->withPaths([
        __FILE__,
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhp74Sets()
    ->withSets([
        PHPUnitSetList::PHPUNIT_90,
    ]);
