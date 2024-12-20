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
        return match (true) {
            PHP_VERSION_ID >= 80100 && PHP_VERSION_ID <= 80199 => SetList::PHP_81,
            PHP_VERSION_ID >= 80200 && PHP_VERSION_ID <= 80299 => SetList::PHP_82,
            true => throw new \Exception('PHP version not supported'),
        };
    }
}
