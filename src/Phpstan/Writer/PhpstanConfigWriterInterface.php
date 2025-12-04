<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Phpstan\Writer;

interface PhpstanConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void;
}
