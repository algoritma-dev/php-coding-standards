<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigFixerWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PhpCsConfigWriterTest extends TestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup();
    }

    public function testWriteConfigFile(): void
    {
        $filename = $this->vfsRoot->url() . '/.php-cs-fixer.dist.php';
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($filename);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/.php-cs-fixer.dist.php';
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/.php-cs-fixer.dist.php';
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($filename, false, true);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDevAndNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/.php-cs-fixer.dist.php';
        $writer = new PhpCsConfigFixerWriter();

        $writer->writeConfigFile($filename, true, true);

        $content = file_get_contents($filename);

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

        static::assertSame($expected, $content);
    }
}
