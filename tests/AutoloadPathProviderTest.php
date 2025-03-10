<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest;

use Algoritma\CodingStandards\AutoloadPathProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class AutoloadPathProviderTest extends TestCase
{
    private string $composerFilePath;

    private string $projectRoot;

    private vfsStreamDirectory $vfsRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup();

        $this->projectRoot = $this->vfsRoot->url();
        $this->composerFilePath = $this->vfsRoot->url() . '/composer.json';
        mkdir($this->vfsRoot->url() . '/src');
        mkdir($this->vfsRoot->url() . '/tests');
        file_put_contents($this->composerFilePath, Util::getComposerContent());
    }

    public function testGetPathsWithDevOn(): void
    {
        $provider = new AutoloadPathProvider(
            $this->composerFilePath,
            $this->projectRoot,
            true,
        );

        $expected = ['src/', 'vfs://root', 'tests/'];
        static::assertSame($expected, $provider->getPaths());
    }

    public function testGetPathsWithDevOff(): void
    {
        $provider = new AutoloadPathProvider(
            $this->composerFilePath,
            $this->projectRoot,
            false,
        );

        $expected = ['src/', 'vfs://root'];
        static::assertSame($expected, $provider->getPaths());
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
        $this->expectExceptionMessage('Unable to get project root');

        new AutoloadPathProvider('wrong/composer/path/');
    }
}
