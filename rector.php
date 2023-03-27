<?php

use Composer\Composer;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $composer = new Composer();
    $symfony = $composer->getRepositoryManager()->findPackage('symfony/frameworkd');

    var_dump($symfony);die();
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::PSR_4,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
    ]);

    $rectorConfig->symfonyContainerPhp('');
};
