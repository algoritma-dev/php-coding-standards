<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector;

use Algoritma\CodingStandards\Shared\SetsProviderInterface;
use Rector\Php\PhpVersionResolver\ComposerJsonPhpVersionResolver;
use Rector\Set\ValueObject\SetList;

class RectorSetsProvider implements SetsProviderInterface
{
    public function getSets(): array
    {
        return [
            SetList::CODE_QUALITY,
            SetList::EARLY_RETURN,
            SetList::INSTANCEOF,
            SetList::DEAD_CODE,
            $this->getPhpSet(),
            SetList::TYPE_DECLARATION,
        ];
    }

    private function getPhpSet(): string
    {
        $version = ComposerJsonPhpVersionResolver::resolveFromCwdOrFail();

        return match (true) {
            $version >= 80100 && $version <= 80199 => SetList::PHP_81,
            $version >= 80200 && $version <= 80299 => SetList::PHP_82,
            $version >= 80300 && $version <= 80399 => SetList::PHP_83,
            $version >= 80400 && $version <= 80499 => SetList::PHP_84,
            $version >= 80500 && $version <= 80599 => SetList::PHP_85,
            true => throw new \Exception('PHP version not supported'),
        };
    }
}
