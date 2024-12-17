<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Writer;

final class PhpCsConfigFixerWriter implements PhpCsConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void
    {
        $filename = $filename ?: '.php-cs-fixer.dist.php';
        file_put_contents($filename, $this->createConfigSource($noDev, $noRisky));
    }

    private function createConfigSource(bool $noDev = false, bool $noRisky = false): string
    {
        $rulesProviderConfig = $this->createRulesProviderConfig($noRisky);

        $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();';

        if ($noDev) {
            $autoloadPathProvider = '$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);';
        }

        return <<<EOD
            <?php

            /*
             * Additional rules or rules to override.
             * These rules will be added to default rules or will override them if the same key already exists.
             */

            {$rulesProviderConfig}

            \$config = new PhpCsFixer\\Config();
            \$config->setRules(\$rulesProvider->getRules());

            \$finder = new PhpCsFixer\\Finder();

            /*
             * You can set manually these paths:
             */
            {$autoloadPathProvider}
            \$finder->in(\$autoloadPathProvider->getPaths());

            \$config->setFinder(\$finder);

            return \$config;

            EOD;
    }

    private function createRulesProviderConfig(bool $noRisky = false): string
    {
        $providersLine = [
            '    new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),',
        ];

        if (false === $noRisky) {
            $providersLine[] = '    new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),';
        }

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
