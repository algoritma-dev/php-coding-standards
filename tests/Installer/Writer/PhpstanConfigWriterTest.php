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

        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename);

        $content = file_get_contents($filename);

        $expected
            = <<<'EOD'
                includes:
                	- phpstan-algoritma-config.php

                parameters:
                	level: 6
                	excludePaths:
                		- **/node_modules/*
                		- **/vendor/*


                EOD;
        self::assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);

        $expected
            = <<<'EOD'
                includes:
                	- phpstan-algoritma-config.php

                parameters:
                	level: 6
                	excludePaths:
                		- **/node_modules/*
                		- **/vendor/*


                EOD;
        self::assertSame($expected, $content);
    }
}
