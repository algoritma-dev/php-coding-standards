<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return RectorConfig::configure()
    ->withImportNames(
        importShortClasses: false
    )
    ->withParallel()
    ->withPaths([
        __FILE__,
        '.php-cs-fixer.dist.php',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    )
    ->withComposerBased(phpunit: true)
    ->withSkip([
        ReturnNeverTypeRector::class,
    ]);
