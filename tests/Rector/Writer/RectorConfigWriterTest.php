<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector\Writer;

use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RectorConfigWriterTest extends TestCase
{
    private string $testFile;

    /**
     * @var list<string>
     */
    private array $magentoDirs = [];

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

        (new Filesystem())->remove($this->magentoDirs);
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

    public function testMagentoWithoutPhpRequirementGetsExplicitPhpSets(): void
    {
        $dir = $this->magentoDir('{"require":{"magento/framework":"*"}}');
        $file = $dir . '/rector.php';

        $this->writerWith([])->writeConfigFile($file);

        $configContent = file_get_contents($file);
        $this->assertStringContainsString(\sprintf('->withPhpSets(php%d%d: true)', \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION), $configContent);
        $this->assertStringNotContainsString('->withPhpSets()', $configContent);
    }

    public function testMagentoWithPhpRequirementKeepsComposerBasedPhpSets(): void
    {
        $dir = $this->magentoDir('{"require":{"php":"~8.3.0"}}');
        $file = $dir . '/rector.php';

        $this->writerWith([])->writeConfigFile($file);

        $this->assertStringContainsString('->withPhpSets()', file_get_contents($file));
    }

    public function testNonMagentoKeepsComposerBasedPhpSets(): void
    {
        $this->writerWith([])->writeConfigFile($this->testFile);

        $this->assertStringContainsString('->withPhpSets()', file_get_contents($this->testFile));
    }

    private function magentoDir(string $composerJson): string
    {
        $dir = sys_get_temp_dir() . '/' . uniqid('rector-magento', true);
        mkdir($dir . '/bin', 0o755, true);
        touch($dir . '/bin/magento');
        file_put_contents($dir . '/composer.json', $composerJson);
        $this->magentoDirs[] = $dir;

        return $dir;
    }

    /**
     * @param list<string> $installed
     */
    private function writerWith(array $installed): RectorConfigWriter
    {
        return new RectorConfigWriter(static fn (string $package): bool => \in_array($package, $installed, true));
    }
}
