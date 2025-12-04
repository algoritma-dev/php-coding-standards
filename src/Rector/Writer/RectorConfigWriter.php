<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector\Writer;

final class RectorConfigWriter implements RectorConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false): void
    {
        $filename = $filename ?: 'rector.php';
        file_put_contents($filename, $this->createConfigSource($noDev));
    }

    private function createConfigSource(bool $noDev = false): string
    {
        $rulesProviderConfig = $this->createRulesProviderConfig();

        $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();';

        if ($noDev) {
            $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);';
        }

        $setsProvider = '$setsProvider = new Algoritma\CodingStandards\Sets\RectorSetsProvider();';

        return <<<EOD
            <?php

            use Rector\\Config\\RectorConfig;

            {$rulesProviderConfig}

            {$autoloadPathProvider}
            
            {$setsProvider}

            return RectorConfig::configure()
                ->withFileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths(\$autoloadPathProvider->getPaths())
                ->withSkip([
                    '**/vendor/*',
                    '**/node_modules/*',
                ])
                ->withPhpSets()
                ->withSets(array_merge(\$setsProvider->getSets(), [/* custom sets */]))
                ->withRules(array_merge(\$rulesProvider->getRules(), [/* custom rules */]));

            EOD;
    }

    private function createRulesProviderConfig(): string
    {
        $providersLine = [
            '    new Algoritma\CodingStandards\Rules\RectorRulesProvider(),',
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
}
