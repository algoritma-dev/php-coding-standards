<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PhpstanConfigWriterTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $vfsRoot;

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
                parameters:
                	level: 8
                	paths:
                		- src/
                		- tests/


                EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename, true);

        $content = file_get_contents($filename);

        $expected
            = <<<'EOD'
                parameters:
                	level: 8
                	paths:
                		- src/


                EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename, false, true);

        $content = file_get_contents($filename);

        $expected
            = <<<'EOD'
                parameters:
                	level: 8
                	paths:
                		- src/
                		- tests/


                EOD;

        $this->assertSame($expected, $content);
    }

    public function testWriteConfigFileWithNoDevAndNoRisky(): void
    {
        $filename = $this->vfsRoot->url() . '/phpstan.neon';
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($filename, true, true);

        $content = file_get_contents($filename);

        $expected
= <<<'EOD'
    parameters:
    	level: 8
    	paths:
    		- src/


    EOD;
        $this->assertSame($expected, $content);
    }
}
