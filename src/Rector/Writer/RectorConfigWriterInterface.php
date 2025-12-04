<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector\Writer;

interface RectorConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false): void;
}
