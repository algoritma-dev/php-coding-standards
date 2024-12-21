<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Sets;

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

        if ($version >= 70400 && $version <= 70499) {
            return SetList::PHP_74;
        }

        if ($version >= 80000 && $version <= 80099) {
            return SetList::PHP_80;
        }

        if ($version >= 80100 && $version <= 80199) {
            return SetList::PHP_81;
        }

        throw new \Exception('PHP version not supported');
    }
}
