<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigFixerWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class PhpCsConfigFixerWriterTest extends TestCase
{
    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = tempnam(sys_get_temp_dir(), 'phpcs');
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
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            /*
             * Additional rules or rules to override.
             * These rules will be added to default rules or will override them if the same key already exists.
             */

            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
                new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);

            $config = new PhpCsFixer\Config();
            $config->setRules($rulesProvider->getRules());
            $config->setRiskyAllowed(true);

            $finder = new PhpCsFixer\Finder();

            /*
             * You can set manually these paths:
             */
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();
            $finder
                ->in($autoloadPathProvider->getPaths())
                ->exclude(['node_modules', '*/vendor/*']);

            $config->setFinder($finder);

            return $config;

            EOD;

        self::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($this->filename, true);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            /*
             * Additional rules or rules to override.
             * These rules will be added to default rules or will override them if the same key already exists.
             */

            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
                new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);

            $config = new PhpCsFixer\Config();
            $config->setRules($rulesProvider->getRules());
            $config->setRiskyAllowed(true);

            $finder = new PhpCsFixer\Finder();

            /*
             * You can set manually these paths:
             */
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);
            $finder
                ->in($autoloadPathProvider->getPaths())
                ->exclude(['node_modules', '*/vendor/*']);

            $config->setFinder($finder);

            return $config;

            EOD;

        self::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoRisky(): void
    {
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($this->filename, false, true);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            /*
             * Additional rules or rules to override.
             * These rules will be added to default rules or will override them if the same key already exists.
             */

            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);

            $config = new PhpCsFixer\Config();
            $config->setRules($rulesProvider->getRules());

            $finder = new PhpCsFixer\Finder();

            /*
             * You can set manually these paths:
             */
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();
            $finder
                ->in($autoloadPathProvider->getPaths())
                ->exclude(['node_modules', '*/vendor/*']);

            $config->setFinder($finder);

            return $config;

            EOD;

        self::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDevAndNoRisky(): void
    {
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($this->filename, true, true);

        $content = file_get_contents($this->filename);

        $expected = <<<'EOD'
            <?php

            /*
             * Additional rules or rules to override.
             * These rules will be added to default rules or will override them if the same key already exists.
             */

            $additionalRules = [];
            $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
                new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
            ]);

            $config = new PhpCsFixer\Config();
            $config->setRules($rulesProvider->getRules());

            $finder = new PhpCsFixer\Finder();

            /*
             * You can set manually these paths:
             */
            $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);
            $finder
                ->in($autoloadPathProvider->getPaths())
                ->exclude(['node_modules', '*/vendor/*']);

            $config->setFinder($finder);

            return $config;

            EOD;

        self::assertSame($expected, $content);
    }
}
