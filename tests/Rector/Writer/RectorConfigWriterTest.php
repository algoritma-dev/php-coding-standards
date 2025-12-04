<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector\Writer;

use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class RectorConfigWriterTest extends TestCase
{
    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = tempnam(sys_get_temp_dir(), 'rector');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function testWriteConfigFile(): void
    {
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            use Rector\Config\RectorConfig;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();
            
            $setsProvider = new Algoritma\CodingStandards\Sets\RectorSetsProvider();

            return RectorConfig::configure()
                ->withFileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths())
                ->withSkip([
                    '**/vendor/*',
                    '**/node_modules/*',
                ])
                ->withPhpSets()
                ->withSets(array_merge($setsProvider->getSets(), [/* custom sets */]))
                ->withRules(array_merge($rulesProvider->getRules(), [/* custom rules */]));

            EOD;

        self::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($this->filename, true);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            use Rector\Config\RectorConfig;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);

            $setsProvider = new Algoritma\CodingStandards\Sets\RectorSetsProvider();

            return RectorConfig::configure()
                ->withFileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths())
                ->withSkip([
                    '**/vendor/*',
                    '**/node_modules/*',
                ])
                ->withPhpSets()
                ->withSets(array_merge($setsProvider->getSets(), [/* custom sets */]))
                ->withRules(array_merge($rulesProvider->getRules(), [/* custom rules */]));

            EOD;

        self::assertSame($expected, $content);
    }
}
