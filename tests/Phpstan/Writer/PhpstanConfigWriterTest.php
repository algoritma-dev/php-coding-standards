<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Phpstan\Writer;

use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class PhpstanConfigWriterTest extends TestCase
{
    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = tempnam(sys_get_temp_dir(), 'phpstan');
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
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

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
        $writer = new PhpstanConfigWriter();

        $writer->writeConfigFile($this->filename, true);

        $content = file_get_contents($this->filename);

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
