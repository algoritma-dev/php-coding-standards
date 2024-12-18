<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Sets;

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
        $version = PHP_VERSION_ID;

        return match (true) {
            $version >= 80_200 && $version <= 80_299 => SetList::PHP_82,
            $version >= 80_300 && $version <= 80_399 => SetList::PHP_83,
            $version >= 80_400 && $version <= 80_499 => SetList::PHP_84,
            true => throw new \Exception('PHP version not supported'),
        };
    }
}
