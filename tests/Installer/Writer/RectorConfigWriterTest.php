<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\RectorConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class RectorConfigWriterTest extends TestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup();
    }

    public function testWriteConfigFile(): void
    {
        $filename = $this->vfsRoot->url() . '/rector.php';
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($filename);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/rector.php';
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }
}
