<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Phpstan\Writer;

use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class PhpstanConfigWriterTest extends TestCase
{
    private string $workDir;

    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDir = sys_get_temp_dir() . '/phpstan-writer-' . uniqid('', true);
        mkdir($this->workDir, 0o755, true);
        $this->filename = $this->workDir . '/phpstan.neon';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeDirectory($this->workDir);
    }

    public function testWriteConfigFile(): void
    {
        $writer = $this->writerWith([]);

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
        self::assertFileDoesNotExist($this->workDir . '/tests/console-application.php');
        self::assertFileDoesNotExist($this->workDir . '/tests/object-manager.php');
    }

    public function testWriteConfigFileWithNoDev(): void
    {
        $writer = $this->writerWith([]);

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

    public function testSymfonyInstalledAddsParametersAndConsoleLoader(): void
    {
        $writer = $this->writerWith(['symfony/framework-bundle']);

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

        self::assertStringContainsString('symfony:', $content);
        self::assertStringContainsString('containerXmlPath: var/cache/dev/App_KernelDevDebugContainer.xml', $content);
        self::assertStringContainsString('consoleApplicationLoader: tests/console-application.php', $content);
        self::assertStringContainsString('scanDirectories:', $content);
        self::assertStringContainsString('var/cache/dev/Symfony/Config', $content);
        self::assertStringContainsString('scanFiles:', $content);
        self::assertStringContainsString('vendor/symfony/dependency-injection/Loader/Configurator/ContainerConfigurator.php', $content);
        self::assertStringNotContainsString('doctrine:', $content);

        $loader = $this->workDir . '/tests/console-application.php';
        self::assertFileExists($loader);
        self::assertStringContainsString('use Symfony\Bundle\FrameworkBundle\Console\Application;', file_get_contents($loader));
        self::assertFileDoesNotExist($this->workDir . '/tests/object-manager.php');
    }

    public function testDoctrineInstalledAddsParametersAndObjectManagerLoader(): void
    {
        $writer = $this->writerWith(['doctrine/orm']);

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

        self::assertStringContainsString('doctrine:', $content);
        self::assertStringContainsString('objectManagerLoader: tests/object-manager.php', $content);
        self::assertStringNotContainsString('symfony:', $content);

        $loader = $this->workDir . '/tests/object-manager.php';
        self::assertFileExists($loader);
        self::assertStringContainsString('ObjectManager', file_get_contents($loader));
        self::assertFileDoesNotExist($this->workDir . '/tests/console-application.php');
    }

    public function testBothFrameworksInstalled(): void
    {
        $writer = $this->writerWith(['symfony/framework-bundle', 'doctrine/orm']);

        $writer->writeConfigFile($this->filename);

        $content = file_get_contents($this->filename);

        self::assertStringContainsString('symfony:', $content);
        self::assertStringContainsString('doctrine:', $content);
        self::assertFileExists($this->workDir . '/tests/console-application.php');
        self::assertFileExists($this->workDir . '/tests/object-manager.php');
    }

    public function testExistingLoaderFileIsNotOverwritten(): void
    {
        mkdir($this->workDir . '/tests', 0o755, true);
        $loader = $this->workDir . '/tests/object-manager.php';
        file_put_contents($loader, '<?php // user content');

        $writer = $this->writerWith(['doctrine/orm']);

        $writer->writeConfigFile($this->filename);

        self::assertSame('<?php // user content', file_get_contents($loader));
    }

    /**
     * @param list<string> $installed
     */
    private function writerWith(array $installed): PhpstanConfigWriter
    {
        return new PhpstanConfigWriter(static fn (string $package): bool => \in_array($package, $installed, true));
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . '/' . $entry;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
