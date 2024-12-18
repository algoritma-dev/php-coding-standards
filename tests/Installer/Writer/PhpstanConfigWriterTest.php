<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PhpstanConfigWriterTest extends TestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup();
    }

    public function testWriteConfigFile(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $rulesFilename = $this->vfsRoot->url() . '/phpstan-rules.php';
        $pathsFilename = $this->vfsRoot->url() . '/phpstan-paths.php';

        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename);

        $content = file_get_contents($filename);
        $rulesContent = file_get_contents($rulesFilename);
        $pathsContent = file_get_contents($pathsFilename);

        $expected
            = <<<'EOD'
                includes:
                	- phpstan-rules.php
                	- phpstan-paths.php

                parameters:
                	level: 8
                	excludePaths:
                		- **/node_modules/*
                		- **/vendor/*

                	type_perfect:
                		no_mixed_property: true
                		no_mixed_caller: true
                		null_over_false: true
                		narrow_param: true
                		narrow_return: true

                	errorFormat: ticketswap
                	editorUrl: 'phpstorm://open?file=%%file%%&line=%%line%%'
                	ignoreErrors: []


                EOD;
        $this->assertSame($expected, $content);

        $expected
            = <<<'EOD'
                <?php

                $additionalRules = [];
                $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                    new Algoritma\CodingStandards\Rules\PhpstanRulesProvider(),
                    new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
                ]);

                return ['rules' => $rulesProvider->getRules()];

                EOD;

        $this->assertSame($expected, $rulesContent);

        $expected
            = <<<'EOD'
                <?php

                $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();
                return ['parameters' => ['paths' => $autoloadPathProvider->getPaths()]];

                EOD;

        $this->assertSame($expected, $pathsContent);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $rulesFilename = $this->vfsRoot->url() . '/phpstan-rules.php';
        $pathsFilename = $this->vfsRoot->url() . '/phpstan-paths.php';
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);
        $rulesContent = file_get_contents($rulesFilename);
        $pathsContent = file_get_contents($pathsFilename);

        $expected
            = <<<'EOD'
                includes:
                	- phpstan-rules.php
                	- phpstan-paths.php

                parameters:
                	level: 8
                	excludePaths:
                		- **/node_modules/*
                		- **/vendor/*

                	type_perfect:
                		no_mixed_property: true
                		no_mixed_caller: true
                		null_over_false: true
                		narrow_param: true
                		narrow_return: true

                	errorFormat: ticketswap
                	editorUrl: 'phpstorm://open?file=%%file%%&line=%%line%%'
                	ignoreErrors: []


                EOD;
        $this->assertSame($expected, $content);

        $expected
            = <<<'EOD'
                <?php

                $additionalRules = [];
                $rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
                    new Algoritma\CodingStandards\Rules\PhpstanRulesProvider(),
                    new Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
                ]);

                return ['rules' => $rulesProvider->getRules()];

                EOD;

        $this->assertSame($expected, $rulesContent);

        $expected
            = <<<'EOD'
                <?php

                $autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider(null, null, false);
                return ['parameters' => ['paths' => $autoloadPathProvider->getPaths()]];

                EOD;

        $this->assertSame($expected, $pathsContent);
    }
}
