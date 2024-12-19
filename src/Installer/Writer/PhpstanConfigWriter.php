<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Writer;

use Nette\Neon\Neon;

final class PhpstanConfigWriter implements PhpCsConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void
    {
        $filename = $filename ?: 'phpstan.neon';
        file_put_contents($filename, $this->createConfigSource());
    }

    private function createConfigSource(): string
    {
        return Neon::encode([
            'includes' => [
                'phpstan-algoritma-config.php',
            ],
            'parameters' => [
                'level' => 6,
                'excludePaths' => [
                    '**/node_modules/*',
                    '**/vendor/*',
                ],
            ],
        ], true);
        // Returns $value converted to multiline NEON
    }
}
