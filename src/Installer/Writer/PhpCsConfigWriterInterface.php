<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Writer;

interface PhpCsConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void;
}
