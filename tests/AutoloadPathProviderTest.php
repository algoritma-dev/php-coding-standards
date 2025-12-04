<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest;

use Algoritma\CodingStandards\AutoloadPathProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class AutoloadPathProviderTest extends TestCase
{
    private string $composerFilePath;

    private string $projectRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRoot = sys_get_temp_dir();
        $this->composerFilePath = tempnam($this->projectRoot, 'composer');
        rename($this->composerFilePath, $this->composerFilePath . '.json');
        $this->composerFilePath .= '.json';

        mkdir($this->projectRoot . '/src');
        mkdir($this->projectRoot . '/tests');
        file_put_contents($this->composerFilePath, Util::getComposerContent());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unlink($this->composerFilePath);
        rmdir($this->projectRoot . '/src');
        rmdir($this->projectRoot . '/tests');
    }

    public function testGetPathsWithDevOn(): void
    {
        $provider = new AutoloadPathProvider(
            $this->composerFilePath,
            $this->projectRoot,
            true,
        );

        $expected = ['src/', $this->projectRoot, 'tests/'];
        self::assertSame($expected, $provider->getPaths());
    }

    public function testGetPathsWithDevOff(): void
    {
        $provider = new AutoloadPathProvider(
            $this->composerFilePath,
            $this->projectRoot,
            false,
        );

        $expected = ['src/', $this->projectRoot];
        self::assertSame($expected, $provider->getPaths());
    }

    public function testGetPathsWithWrongComposerJsonPath(): void
    {
        $provider = new AutoloadPathProvider(__DIR__ . '/composer.json');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find composer.json');

        $provider->getPaths();
    }

    public function testGetPathsWithInvalidComposerJson(): void
    {
        $provider = new AutoloadPathProvider(
            $this->composerFilePath,
            $this->projectRoot,
            false,
        );

        file_put_contents($this->composerFilePath, '');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid composer.json file');

        $provider->getPaths();
    }

    public function testWrongComposerPathLeadsToBrokenProjectPath(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to get project root.');

        new AutoloadPathProvider('wrong/composer/path/');
    }

    public function testGetPathsWithComposerEnvVar(): void
    {
        putenv('COMPOSER=' . $this->composerFilePath);

        $provider = new AutoloadPathProvider(
            null,
            $this.projectRoot,
            true,
        );

        $expected = ['src/', $this->projectRoot, 'tests/'];
        self::assertSame($expected, $provider->getPaths());

        putenv('COMPOSER=');
    }
}
