<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Writer;

use Algoritma\CodingStandards\AutoloadPathProvider;
use Nette\Neon\Neon;

final class PhpstanConfigWriter implements PhpCsConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void
    {
        $filename = $filename ?: 'phpstan.neon';
        file_put_contents($filename, $this->createConfigSource($noDev));
    }

    private function createConfigSource(bool $noDev = false): string
    {
        $autoloadPathProvider = new AutoloadPathProvider();

        if ($noDev) {
            $autoloadPathProvider = new AutoloadPathProvider(null, null, false);
        }

        return Neon::encode([
            'parameters' => [
                'level' => 8,
                'paths' => $autoloadPathProvider->getPaths(),
            ],
        ], true); // Returns $value converted to multiline NEON
    }
}
