<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rules = [
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PSR_4,
        SetList::NAMING,
        SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
    ];

    $symfony = getenv('SYMFONY');
    $doctrine = getenv('DOCTRINE');

    if ($symfony) {
        $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');

        $rules = array_merge($rules, [
            SymfonySetList::SYMFONY_62,
            SymfonySetList::SYMFONY_CODE_QUALITY,
            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        ]);
    }

    if ($doctrine) {
        $rectorConfig->sets([
            DoctrineSetList::DOCTRINE_CODE_QUALITY,
        ]);
    }

    $rectorConfig->sets($rules);
};
