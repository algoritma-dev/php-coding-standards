<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer;

use PHPUnit\Framework\Attributes\DataProvider;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Algoritma\CodingStandards\Installer\Installer;
use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigWriterInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use Algoritma\CodingStandardsTest\Util;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Prophecy\Argument;

class InstallerTest extends TestCase
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
        file_put_contents($this->composerFilePath, Util::getComposerContent());
    }

    /**
     * @return array{array{string, string}, array{string, string}}[]
     */
    public static function invalidUpgradeProvider(): array
    {
        return [
            [['0.1.0.0', '0.1.0'], ['0.1.0.0', '0.1.0']],
            [['dev-master', 'dev-master'], ['dev-master', 'dev-master']],
            [['dev-master#12345', 'dev-master'], ['dev-master#12345', 'dev-master']],
            [['dev-master#12345', 'dev-master'], ['dev-master#12346', 'dev-master']],
        ];
    }

    /**
     * @return array{array{string, string}, array{string, string}}[]
     */
    public static function validUpgradeProvider(): array
    {
        return [
            [['0.1.0.0', '0.1.0'], ['0.2.0.0', '0.2.0']],
            [['0.1.0.1', '0.1.1'], ['0.1.0.2', '0.1.2']],
            [['0.1.0.0', '0.1.0'], ['1.0.0.0', '1.0.0']],
            [['1.0.0.0', '1.0.0'], ['2.0.0.0', '2.0.0']],
            [['dev-master', 'dev-master'], ['dev-feature', 'dev-feature']],
        ];
    }

    /**
     * @param array{string, string} $currentPackageV
     * @param array{string, string} $targetPackageV
     */
    #[DataProvider('invalidUpgradeProvider')]
    public function testCheckUpgradeTestNotNecessary(array $currentPackageV, array $targetPackageV): void
    {
        $currentPackage = new Package('dummy', $currentPackageV[0], $currentPackageV[1]);
        $targetPackage = new Package('dummy', $targetPackageV[0], $targetPackageV[1]);

        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanAlgoritmaWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $rectorWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
            $phpstanWriter->reveal(),
            $phpstanAlgoritmaWriter->reveal(),
            $rectorWriter->reveal(),
        );

        $io->isInteractive()
            ->willReturn(true);
        $io->askConfirmation(Argument::cetera())
            ->shouldNotBeCalled();

        $installer->checkUpgrade($currentPackage, $targetPackage);
    }

    /**
     * @param array{string, string} $currentPackageV
     * @param array{string, string} $targetPackageV
     */
    #[DataProvider('validUpgradeProvider')]
    public function testCheckUpgradeTestNecessary(array $currentPackageV, array $targetPackageV): void
    {
        $currentPackage = new Package('dummy', $currentPackageV[0], $currentPackageV[1]);
        $targetPackage = new Package('dummy', $targetPackageV[0], $targetPackageV[1]);

        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanAlgoritmaWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $rectorWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
            $phpstanWriter->reveal(),
            $phpstanAlgoritmaWriter->reveal(),
            $rectorWriter->reveal(),
        );

        $io->isInteractive()
            ->willReturn(true);
        $io->askConfirmation(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn(false);

        $installer->checkUpgrade($currentPackage, $targetPackage);
    }

    public function testCheckUpgrade(): void
    {
        $currentPackage = new Package('dummy', '0.1.0', '0.1.0');
        $targetPackage = new Package('dummy', '0.2.0', '0.2.0');

        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $phpstanAlgoritmaWriter = $this->prophesize(PhpCsConfigWriterInterface::class);
        $rectorWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
            $phpstanWriter->reveal(),
            $phpstanAlgoritmaWriter->reveal(),
            $rectorWriter->reveal(),
        );

        $io->isInteractive()
            ->willReturn(true);
        $io->askConfirmation(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn(true);

        $io->write(Argument::type('string'))->shouldBeCalled();

        $phpstanAlgoritmaWriter->writeConfigFile($this->projectRoot . '/phpstan-algoritma-config.php')
            ->shouldBeCalled();

        $installer->checkUpgrade($currentPackage, $targetPackage);
    }

    public function testCheckUpgradeShouldntWriteWithNoInteractiveInput(): void
    {
        $currentPackage = new Package('dummy', '0.1.0', '0.1.0');
        $targetPackage = new Package('dummy', '0.2.0', '0.2.0');

        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
        );

        $io->isInteractive()
            ->willReturn(false);
        $io->askConfirmation(Argument::cetera())
            ->shouldNotBeCalled();

        $io->write(Argument::containingString('Skip'))->shouldBeCalled();
        $phpCsWriter->writeConfigFile(Argument::cetera())
            ->shouldNotBeCalled();

        $installer->checkUpgrade($currentPackage, $targetPackage);
    }

    public function testCreatePhpCsFixerConfigWithAlreadyExistingFile(): void
    {
        touch($this->projectRoot . '/.php-cs-fixer.dist.php');

        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $phpCsWriter->writeConfigFile(Argument::cetera())->shouldNotBeCalled();
        $io->write(Argument::any())->shouldBeCalled();
        $io->askConfirmation(Argument::cetera())->shouldNotBeCalled();
        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
        );
        $installer->createPhpCsConfig();
    }

    public function testCreatePhpstanConfigWithAlreadyExistingFile(): void
    {
        touch($this->projectRoot . '/phpstan.neon');

        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $writer->writeConfigFile(Argument::cetera())->shouldNotBeCalled();
        $io->write(Argument::any())->shouldBeCalled();
        $io->askConfirmation(Argument::cetera())->shouldNotBeCalled();
        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            $writer->reveal(),
        );
        $installer->createPhpstanConfig();
    }

    public function testCreatePhpstanAlgoritmaConfigWithAlreadyExistingFileShouldWriteAnyway(): void
    {
        touch($this->projectRoot . '/phpstan-algoritma-config.neon');

        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $writer->writeConfigFile(Argument::cetera())->shouldBeCalled();
        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            null,
            $writer->reveal(),
        );
        $installer->createPhpstanAlgoritmaConfig();
    }

    public function testCreateRectorConfigWithAlreadyExistingFile(): void
    {
        touch($this->projectRoot . '/rector.php');

        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $writer->writeConfigFile(Argument::cetera())->shouldNotBeCalled();
        $io->write(Argument::any())->shouldBeCalled();
        $io->askConfirmation(Argument::cetera())->shouldNotBeCalled();
        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            null,
            null,
            $writer->reveal(),
        );
        $installer->createRectorConfig();
    }

    public function testRequestCreatePhpCsFixerConfig(): void
    {
        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $phpCsWriter = $this->prophesize(PhpCsConfigWriterInterface::class);

        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $package->getDevAutoload()->willReturn([]);
        $phpCsWriter->writeConfigFile($this->projectRoot . '/.php-cs-fixer.dist.php', false, true)
            ->shouldBeCalled();

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            $phpCsWriter->reveal(),
        );

        $installer->createPhpCsConfig();
    }

    public function testRequestCreatePhpstanConfig(): void
    {
        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $writer->writeConfigFile($this->projectRoot . '/phpstan.neon')
            ->shouldBeCalled();
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            $writer->reveal(),
        );

        $installer->createPhpstanConfig();
    }

    public function testCreatePhpstanAlgoritmaConfig(): void
    {
        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $writer->writeConfigFile($this->projectRoot . '/phpstan-algoritma-config.php')->shouldBeCalled();
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            null,
            $writer->reveal(),
        );

        $installer->createPhpstanAlgoritmaConfig();
    }

    public function testRequestCreateRectorConfig(): void
    {
        $package = $this->prophesize(PackageInterface::class);
        $io = $this->prophesize(IOInterface::class);
        $composer = $this->prophesize(Composer::class);
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);

        $composer->getPackage()->willReturn($package);
        $package->getAutoload()->willReturn([]);
        $writer->writeConfigFile($this->projectRoot . '/rector.php')
            ->shouldBeCalled();
        $package->getDevAutoload()->willReturn([]);

        $installer = new Installer(
            $io->reveal(),
            $composer->reveal(),
            $this->projectRoot,
            $this->composerFilePath,
            null,
            null,
            null,
            $writer->reveal(),
        );

        $installer->createRectorConfig();
    }
}
