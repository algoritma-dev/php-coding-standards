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

            use RectorConfigRectorConfig;
            use RectorPHPUnitSetPHPUnitSetList;
            use RectorTypeDeclarationRectorClassMethodReturnNeverTypeRector;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();

            return RectorConfig::configure()
                ->fileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths()})
                ->withPhpSets()
                ->withPreparedSets(
                    deadCode: true,
                    codeQuality: true,
                    typeDeclarations: true,
                    instanceOf: true,
                    codingStyle: true,
                    phpunitCodeQuality: true,
                    earlyReturn: true,
                )->withRules($rulesProvider->getRules());

            EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/rector.php';
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);

        $expected = <<<'EOD'
            <?php

            use RectorConfigRectorConfig;
            use RectorPHPUnitSetPHPUnitSetList;
            use RectorTypeDeclarationRectorClassMethodReturnNeverTypeRector;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);

            return RectorConfig::configure()
                ->fileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths()})
                ->withPhpSets()
                ->withPreparedSets(
                    deadCode: true,
                    codeQuality: true,
                    typeDeclarations: true,
                    instanceOf: true,
                    codingStyle: true,
                    phpunitCodeQuality: true,
                    earlyReturn: true,
                )->withRules($rulesProvider->getRules());

            EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/rector.php';
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($filename, false, true);

        $content = file_get_contents($filename);

        $expected = <<<'EOD'
            <?php

            use RectorConfigRectorConfig;
            use RectorPHPUnitSetPHPUnitSetList;
            use RectorTypeDeclarationRectorClassMethodReturnNeverTypeRector;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();

            return RectorConfig::configure()
                ->fileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths()})
                ->withPhpSets()
                ->withPreparedSets(
                    deadCode: true,
                    codeQuality: true,
                    typeDeclarations: true,
                    instanceOf: true,
                    codingStyle: true,
                    phpunitCodeQuality: true,
                    earlyReturn: true,
                )->withRules($rulesProvider->getRules());

            EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDevAndNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/rector.php';
        $writer = new RectorConfigWriter();

        $writer->writeConfigFile($filename, true, true);

        $content = file_get_contents($filename);

        $expected = <<<'EOD'
            <?php

            use RectorConfigRectorConfig;
            use RectorPHPUnitSetPHPUnitSetList;
            use RectorTypeDeclarationRectorClassMethodReturnNeverTypeRector;
            
            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\RectorRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);
            
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);

            return RectorConfig::configure()
                ->fileExtensions(['php'])
                ->withImportNames(importShortClasses: false)
                ->withParallel()
                ->withPaths($autoloadPathProvider->getPaths()})
                ->withPhpSets()
                ->withPreparedSets(
                    deadCode: true,
                    codeQuality: true,
                    typeDeclarations: true,
                    instanceOf: true,
                    codingStyle: true,
                    phpunitCodeQuality: true,
                    earlyReturn: true,
                )->withRules($rulesProvider->getRules());

            EOD;

        $this->assertSame($expected, $content);
    }
}
