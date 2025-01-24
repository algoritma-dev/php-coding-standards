<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Writer;

final class PHPMDConfigWriter implements PhpCsConfigWriterInterface
{
    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void
    {
        $filename = $filename ?: 'phpmd.xml';
        file_put_contents($filename, $this->createConfigSource($noDev));
    }

    private function createConfigSource(bool $noDev = false): string
    {
        return <<<EOD
            <?xml version="1.0"?>
            <ruleset
                name="algoritma/php-coding-standards"
                xmlns="http://pmd.sf.net/ruleset/1.0.0"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
                xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd"
            >
            <!-- PHPMD custom configuration -->
            </ruleset>
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
