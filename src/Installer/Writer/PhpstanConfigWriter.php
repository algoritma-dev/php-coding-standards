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
        file_put_contents(dirname($filename) . '/phpstan-rules.php', $this->createRulesFile());
        file_put_contents(dirname($filename) . '/phpstan-paths.php', $this->createPathsFile($noDev));
    }

    private function createConfigSource(): string
    {
        return Neon::encode([
            'includes' => [
                'phpstan-rules.php',
                'phpstan-paths.php',
            ],
            'parameters' => [
                'level' => 8,
                'excludePaths' => [
                    '**/node_modules/*',
                    '**/vendor/*',
                ],
                'type_perfect' => [
                    'no_mixed_property' => true,
                    'no_mixed_caller' => true,
                    'null_over_false' => true,
                    'narrow_param' => true,
                    'narrow_return' => true,
                ],
                'errorFormat' => 'ticketswap',
                'editorUrl' => 'phpstorm://open?file=%%file%%&line=%%line%%',
                'ignoreErrors' => [],
            ],
        ], true);
        // Returns $value converted to multiline NEON
    }

    private function createRulesProviderConfig(): string
    {
        $providersLine = [
            '    new Algoritma\CodingStandards\Rules\PhpstanRulesProvider(),',
        ];
        $providersLine[] = '    new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),';
        $providersLine = implode("\n", $providersLine);

        return <<<EOD
            \$additionalRules = [];
            \$rulesProvider = new Algoritma\\CodingStandards\\Rules\\CompositeRulesProvider([
            {$providersLine}
            ]);
            EOD;
    }

    private function createPathsFile(bool $noDev): string
    {
        $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();';

        if ($noDev) {
            $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);';
        }

        return <<<EOD
            <?php
            
            {$autoloadPathProvider}
            return ['parameters' => ['paths' => \$autoloadPathProvider->getPaths()]];

            EOD;
    }

    private function createRulesFile(): string
    {
        $rulesProviderConfig = $this->createRulesProviderConfig();

        return <<<EOD
            <?php

            {$rulesProviderConfig}

            return ['rules' => \$rulesProvider->getRules()];

            EOD;
    }
}
