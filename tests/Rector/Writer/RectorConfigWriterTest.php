<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector\Writer;

use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriter;
use PHPUnit\Framework\TestCase;

class RectorConfigWriterTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFile = tempnam(sys_get_temp_dir(), 'rector-config-test');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testDoctrineConfigIsWrittenWhenDoctrineIsInstalled(): void
    {
        $writer = $this->writerWith(['doctrine/orm']);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringContainsString('->withComposerBased(doctrine: true)', $configContent);
    }

    public function testDoctrineConfigIsNotWrittenWhenDoctrineIsNotInstalled(): void
    {
        $writer = $this->writerWith([]);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringNotContainsString('->withComposerBased(doctrine: true)', $configContent);
    }

    public function testSymfonyConfigIsWrittenWhenSymfonyIsInstalled(): void
    {
        $writer = $this->writerWith(['symfony/framework-bundle']);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withComposerBased(symfony: true)', $configContent);
        $this->assertStringContainsString('->withSymfonyContainerXml(', $configContent);
    }

    public function testSymfonyConfigIsNotWrittenWhenSymfonyIsNotInstalled(): void
    {
        $writer = $this->writerWith([]);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withComposerBased(symfony: true)', $configContent);
    }

    public function testLaravelConfigIsWrittenWhenLaravelIsInstalled(): void
    {
        $writer = $this->writerWith(['laravel/framework']);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withSetProviders([Rector\Laravel\Set\LaravelSetProvider::class])', $configContent);
        $this->assertStringContainsString('->withComposerBased(laravel: true)', $configContent);
    }

    public function testLaravelConfigIsNotWrittenWhenLaravelIsNotInstalled(): void
    {
        $writer = $this->writerWith([]);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withSetProviders([Rector\Laravel\Set\LaravelSetProvider::class])', $configContent);
        $this->assertStringNotContainsString('->withComposerBased(laravel: true)', $configContent);
    }

    public function testDoctrineAndSymfonyConfigIsWrittenWhenBothAreInstalled(): void
    {
        $writer = $this->writerWith(['doctrine/orm', 'symfony/framework-bundle']);
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringContainsString('->withComposerBased(doctrine: true, symfony: true)', $configContent);
    }

    /**
     * @param list<string> $installed
     */
    private function writerWith(array $installed): RectorConfigWriter
    {
        return new RectorConfigWriter(static fn (string $package): bool => \in_array($package, $installed, true));
    }
}
