<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector\Writer;

use Composer\InstalledVersions;

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

        $setsProvider = '$setsProvider = new Algoritma\CodingStandards\Rector\RectorSetsProvider();';

        $rectorConfig = 'return RectorConfig::configure()';
        $rectorConfig .= "\n" . '    ->withFileExtensions([\'php\'])';
        $rectorConfig .= "\n" . '    ->withImportNames(importShortClasses: false)';
        $rectorConfig .= "\n" . '    ->withParallel()';
        $rectorConfig .= "\n" . '    ->withPaths($autoloadPathProvider->getPaths())';
        $rectorConfig .= "\n" . '    ->withSkip([\'**/vendor/*\', \'**/node_modules/*\'])';
        $rectorConfig .= "\n" . '    ->withPhpSets()';

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('laravel/framework')) {
            $rectorConfig .= "\n" . '    ->withSetProviders([Rector\Laravel\Set\LaravelSetProvider::class])';
        }

        $withComposerBased = [];
        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('doctrine/orm')) {
            $rectorConfig .= "\n" . '    ->withPreparedSets(doctrineCodeQuality: true)';
            $withComposerBased[] = 'doctrine: true';
        }

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('symfony/framework-bundle')) {
            $withComposerBased[] = 'symfony: true';
            $rectorConfig .= "\n" . '    ->withSymfonyContainerXml(__DIR__ . \'/var/cache/dev/App_KernelDevDebugContainer.xml\')';
        }

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('laravel/framework')) {
            $withComposerBased[] = 'laravel: true';
        }

        if ($withComposerBased !== []) {
            $rectorConfig .= "\n" . '    ->withComposerBased(' . implode(', ', $withComposerBased) . ')';
        }

        $rectorConfig .= "\n" . '    ->withSets(array_merge($setsProvider->getSets(), [/* custom sets */]))';
        $rectorConfig .= "\n" . '    ->withRules(array_merge($rulesProvider->getRules(), [/* custom rules */]));';

        return <<<EOD
            <?php

            use Rector\\Config\\RectorConfig;

            {$rulesProviderConfig}

            {$autoloadPathProvider}
            
            {$setsProvider}

            {$rectorConfig}

            EOD;
    }

    private function createRulesProviderConfig(): string
    {
        $providersLine = [];
        $providersLine[] = '    new Algoritma\CodingStandards\Shared\Rules\ArrayRulesProvider($additionalRules),';
        $providersLine = implode("\n", $providersLine);

        return <<<EOD
            \$additionalRules = [];
            \$rulesProvider = new Algoritma\\CodingStandards\\Shared\\Rules\\CompositeRulesProvider([
            {$providersLine}
            ]);
            EOD;
    }
}
