<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Writer;

use Algoritma\CodingStandards\Installer\Writer\PHPMDConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class PHPMDConfigWriterTest extends TestCase
{
    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup();
    }

    public function testWriteConfigFile(): void
    {
        $filename = $this->vfsRoot->url() . '/phpmd.xml';
        $writer = new PHPMDConfigWriter();

        $writer->writeConfigFile($filename);

        $content = file_get_contents($filename);

        $expected = <<<'EOD'
            <?xml version="1.0"?>
            <ruleset
                name="algoritma/php-coding-standards"
                xmlns="http://pmd.sf.net/ruleset/1.0.0"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0 http://pmd.sf.net/ruleset_xml_schema.xsd"
                xsi:noNamespaceSchemaLocation="http://pmd.sf.net/ruleset_xml_schema.xsd"
            >
            <!-- Algoritma RuleSets are load automatically (by using the algphpmd binary) -->
            <!-- PHPMD custom configuration HERE -->
            </ruleset>
            EOD;

        self::assertSame($expected, $content);
    }
}
