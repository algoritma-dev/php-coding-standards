<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Composer;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements EventSubscriberInterface, PluginInterface, Capable
{
    public function __construct(private ?Installer $installer = null) {}

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function getPackageName(): string
    {
        $composerJson = new JsonFile(\dirname(__DIR__, 2) . '/composer.json');
        /** @var array{name: string} $composerDefinition */
        $composerDefinition = $composerJson->read();

        return $composerDefinition['name'];
    }

    /**
     * @return array<string, string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
        ];
    }

    /**
     * Apply plugin modifications to Composer.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function activate(Composer $composer, IOInterface $io): void {}

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getInstaller(IOInterface $io): Installer
    {
        if (! $this->installer instanceof Installer) {
            $this->installer = new Installer($io);
        }

        return $this->installer;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function onPostPackageUpdate(PackageEvent $event): void
    {
        if (! $event->isDevMode()) {
            return;
        }

        $operation = $event->getOperation();

        if (! $operation instanceof UpdateOperation) {
            return;
        }

        $package = $operation->getInitialPackage();
        $name = $package->getName();

        if ($name !== self::getPackageName()) {
            return;
        }

        $installer = $this->getInstaller($event->getIO());

        $installer->checkUpgrade($operation->getInitialPackage(), $operation->getTargetPackage());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function onPostPackageInstall(PackageEvent $event): void
    {
        if (! $event->isDevMode()) {
            return;
        }

        $operation = $event->getOperation();

        if (! $operation instanceof InstallOperation) {
            return;
        }

        $package = $operation->getPackage();
        $name = $package->getName();

        if ($name !== self::getPackageName()) {
            return;
        }

        $installer = $this->getInstaller($event->getIO());
        $installer->installCommands();
    }

    /**
     * @return string[]
     */
    public function getCapabilities(): array
    {
        return [
            ComposerCommandProvider::class => CommandProvider::class,
        ];
    }

    public function deactivate(Composer $composer, IOInterface $io): void {}

    public function uninstall(Composer $composer, IOInterface $io): void {}
}
