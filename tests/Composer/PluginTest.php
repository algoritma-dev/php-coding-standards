<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Composer;

use Algoritma\CodingStandards\Composer\CommandProvider;
use Algoritma\CodingStandards\Composer\Installer;
use Algoritma\CodingStandards\Composer\Plugin;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Prophecy\Argument;

class PluginTest extends TestCase
{
    private const PACKAGE_NAME = 'algoritma/php-coding-standards';

    public function testGetPackageName(): void
    {
        $packageName = Plugin::getPackageName();
        self::assertEquals(self::PACKAGE_NAME, $packageName);
    }

    public function testGetSubscribedEvents(): void
    {
        $plugin = new Plugin();
        self::assertInstanceOf(PluginInterface::class, $plugin);
        self::assertInstanceOf(EventSubscriberInterface::class, $plugin);
        $events = Plugin::getSubscribedEvents();

        self::assertCount(2, $events);

        self::assertArrayHasKey(PackageEvents::POST_PACKAGE_INSTALL, $events);
        self::assertArrayHasKey(PackageEvents::POST_PACKAGE_UPDATE, $events);

        self::assertTrue(method_exists($plugin, $events[PackageEvents::POST_PACKAGE_INSTALL]));
        self::assertTrue(method_exists($plugin, $events[PackageEvents::POST_PACKAGE_UPDATE]));
    }

    public function testActive(): void
    {
        $plugin = new Plugin();

        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $plugin->activate($composer->reveal(), $io->reveal());

        // assert no exceptions
        // @phpstan-ignore-next-line
        self::assertTrue(true);
    }

    public function testGetInstallerAfterSetter(): void
    {
        $io = $this->prophesize(IOInterface::class);
        $installer = $this->prophesize(Installer::class);

        $plugin = new Plugin($installer->reveal());

        $installerInstance = $plugin->getInstaller($io->reveal());

        self::assertInstanceOf(Installer::class, $installerInstance);
        self::assertSame($installer->reveal(), $installerInstance);
    }

    public function testOnPostPackageUpdate(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(UpdateOperation::class);
        $package = $this->prophesize(PackageInterface::class);
        $targetPackage = $this->prophesize(PackageInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $operation->getInitialPackage()->willReturn($package->reveal());
        $operation->getTargetPackage()->willReturn($targetPackage->reveal());
        $package->getName()->willReturn(self::PACKAGE_NAME);

        $plugin = new Plugin($installer->reveal());
        $installer->checkUpgrade($package, $targetPackage)->shouldBeCalled();

        $plugin->onPostPackageUpdate($event->reveal());
    }

    public function testOnPostPackageUpdateInNoDevMode(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(UpdateOperation::class);
        $installer = $this->prophesize(Installer::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(false);
        $installer->checkUpgrade(Argument::cetera())->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());

        $plugin->onPostPackageUpdate($event->reveal());
    }

    public function testOnPostPackageUpdateWithAnotherOperation(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(OperationInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $installer->checkUpgrade(Argument::cetera())->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());
        $plugin->onPostPackageUpdate($event->reveal());
    }

    public function testOnPostPackageUpdateWithAnotherPackage(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(UpdateOperation::class);
        $package = $this->prophesize(PackageInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $operation->getInitialPackage()->willReturn($package->reveal());
        $package->getName()->shouldBeCalled()->willReturn('foo');
        $installer->checkUpgrade(Argument::cetera())->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());
        $plugin->onPostPackageUpdate($event->reveal());
    }

    public function testOnPostPackageInstall(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(InstallOperation::class);
        $package = $this->prophesize(PackageInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $operation->getPackage()->willReturn($package->reveal());
        $package->getName()->willReturn(self::PACKAGE_NAME);

        $plugin = new Plugin($installer->reveal());
        $installer->installCommands()->shouldBeCalled();

        $plugin->onPostPackageInstall($event->reveal());
    }

    public function testOnPostPackageInstallInNoDevMode(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(InstallOperation::class);
        $installer = $this->prophesize(Installer::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(false);
        $installer->installCommands()->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());

        $plugin->onPostPackageInstall($event->reveal());
    }

    public function testOnPostPackageInstallWithAnotherOperation(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(OperationInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $installer->installCommands()->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());
        $plugin->onPostPackageInstall($event->reveal());
    }

    public function testOnPostPackageInstallWithAnotherPackage(): void
    {
        $event = $this->prophesize(PackageEvent::class);
        $operation = $this->prophesize(InstallOperation::class);
        $package = $this->prophesize(PackageInterface::class);
        $installer = $this->prophesize(Installer::class);
        $composer = $this->prophesize(Composer::class);
        $io = $this->prophesize(IOInterface::class);

        $event->getOperation()->willReturn($operation->reveal());
        $event->isDevMode()->willReturn(true);
        $event->getComposer()->willReturn($composer->reveal());
        $event->getIO()->willReturn($io->reveal());
        $operation->getPackage()->willReturn($package->reveal());
        $package->getName()->shouldBeCalled()->willReturn('foo');
        $installer->installCommands()->shouldNotBeCalled();

        $plugin = new Plugin($installer->reveal());
        $plugin->onPostPackageInstall($event->reveal());
    }

    public function testCapabilities(): void
    {
        $plugin = new Plugin();

        self::assertInstanceOf(Capable::class, $plugin);

        $capabilities = $plugin->getCapabilities();

        self::assertArrayHasKey(CommandProvider::class, $capabilities);
        self::assertSame(CommandProvider::class, $capabilities[CommandProvider::class]);
    }
}
