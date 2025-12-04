<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector\Writer;

use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriter;
use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

class RectorConfigWriterTest extends TestCase
{
    private string $testFile;

    /**
     * @var array{root: array<mixed>, versions: array<mixed>}
     */
    private static array $installedVersionsData;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$installedVersionsData = InstalledVersions::getAllRawData()[0];
    }

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
        InstalledVersions::reload(self::$installedVersionsData);
    }

    public function testDoctrineConfigIsWrittenWhenDoctrineIsInstalled(): void
    {
        $this->mockInstalledVersions('doctrine/orm', '3.0.0');

        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringContainsString('->withComposerBased(doctrine: true)', $configContent);
    }

    public function testDoctrineConfigIsNotWrittenWhenDoctrineIsNotInstalled(): void
    {
        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringNotContainsString('->withComposerBased(doctrine: true)', $configContent);
    }

    public function testSymfonyConfigIsWrittenWhenSymfonyIsInstalled(): void
    {
        $this->mockInstalledVersions('symfony/framework-bundle', '6.0.0');

        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withComposerBased(symfony: true)', $configContent);
    }

    public function testSymfonyConfigIsNotWrittenWhenSymfonyIsNotInstalled(): void
    {
        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withComposerBased(symfony: true)', $configContent);
    }

    public function testLaravelConfigIsWrittenWhenLaravelIsInstalled(): void
    {
        $this->mockInstalledVersions('laravel/framework', '10.0.0');

        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withSetProviders([Rector\Laravel\Set\LaravelSetProvider::class])', $configContent);
        $this->assertStringContainsString('->withComposerBased(laravel: true)', $configContent);
    }

    public function testLaravelConfigIsNotWrittenWhenLaravelIsNotInstalled(): void
    {
        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringNotContainsString('->withSetProviders([Rector\Laravel\Set\LaravelSetProvider::class])', $configContent);
        $this->assertStringNotContainsString('->withComposerBased(laravel: true)', $configContent);
    }

    public function testDoctrineAndSymfonyConfigIsWrittenWhenBothAreInstalled(): void
    {
        $this->mockInstalledVersions([
            'doctrine/orm' => '3.0.0',
            'symfony/framework-bundle' => '6.0.0',
        ]);

        $writer = new RectorConfigWriter();
        $writer->writeConfigFile($this->testFile);

        $configContent = file_get_contents($this->testFile);

        $this->assertStringContainsString('->withPreparedSets(doctrineCodeQuality: true)', $configContent);
        $this->assertStringContainsString('->withComposerBased(doctrine: true, symfony: true)', $configContent);
    }

    /**
     * @param array<string, string|null>|string $packages
     */
    private function mockInstalledVersions(array|string $packages, ?string $version = null): void
    {
        $versions = [];
        if (is_string($packages)) {
            $packages = [$packages => $version];
        }

        foreach ($packages as $package => $ver) {
            $versions[$package] = [
                'pretty_version' => $ver,
                'version' => $ver,
                'aliases' => [],
                'reference' => 'mock',
                'dev_requirement' => true,
            ];
        }

        $data = [
            'root' => [
                'name' => 'algoritma/php-coding-standards',
                'pretty_version' => '1.0.0',
                'version' => '1.0.0',
                'reference' => 'mock',
                'type' => 'library',
                'install_path' => __DIR__ . '/../../..',
                'aliases' => [],
                'dev' => true,
            ],
            'versions' => $versions,
        ];

        InstalledVersions::reload($data);
    }
}
