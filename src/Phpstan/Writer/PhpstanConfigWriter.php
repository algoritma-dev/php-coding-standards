<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Phpstan\Writer;

use Composer\InstalledVersions;
use Nette\Neon\Neon;

final class PhpstanConfigWriter implements PhpstanConfigWriterInterface
{
    /** @var callable(string): bool */
    private $packageDetector;

    /**
     * @param (callable(string): bool)|null $packageDetector resolves whether a Composer package is installed
     */
    public function __construct(?callable $packageDetector = null)
    {
        $this->packageDetector = $packageDetector ?? static fn (string $package): bool => class_exists(InstalledVersions::class) && InstalledVersions::isInstalled($package);
    }

    public function writeConfigFile(?string $filename = null, bool $noDev = false, bool $noRisky = false): void
    {
        $filename = $filename ?: 'phpstan.neon';
        file_put_contents($filename, $this->createConfigSource());

        $baseDir = \dirname($filename);

        if ($this->isInstalled('symfony/framework-bundle')) {
            $this->writeLoaderFile($baseDir . '/tests/console-application.php', $this->createConsoleApplicationLoader());
        }

        if ($this->isInstalled('doctrine/orm')) {
            $this->writeLoaderFile($baseDir . '/tests/object-manager.php', $this->createObjectManagerLoader());
        }
    }

    private function createConfigSource(): string
    {
        $parameters = [
            'level' => 6,
            'excludePaths' => [
                '**/node_modules/*',
                '**/vendor/*',
            ],
        ];

        if ($this->isInstalled('symfony/framework-bundle')) {
            $parameters['symfony'] = [
                'containerXmlPath' => 'var/cache/dev/App_KernelDevDebugContainer.xml',
                'consoleApplicationLoader' => 'tests/console-application.php',
            ];
        }

        if ($this->isInstalled('doctrine/orm')) {
            $parameters['doctrine'] = [
                'objectManagerLoader' => 'tests/object-manager.php',
            ];
        }

        return Neon::encode([
            'includes' => [
                'phpstan-algoritma-config.php',
            ],
            'parameters' => $parameters,
        ], true);
        // Returns $value converted to multiline NEON
    }

    private function isInstalled(string $package): bool
    {
        return ($this->packageDetector)($package);
    }

    private function writeLoaderFile(string $path, string $content): void
    {
        if (file_exists($path)) {
            return;
        }

        $directory = \dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0o755, true);
        }

        file_put_contents($path, $content);
    }

    private function createObjectManagerLoader(): string
    {
        return <<<'EOD'
            <?php

            declare(strict_types=1);

            // PHPStan Doctrine object manager loader.
            // Replace the body with your application's entity manager bootstrap.
            // See https://github.com/phpstan/phpstan-doctrine#configuration

            require __DIR__ . '/../vendor/autoload.php';

            throw new \RuntimeException('Configure tests/object-manager.php to return your Doctrine ObjectManager.');

            EOD;
    }

    private function createConsoleApplicationLoader(): string
    {
        return <<<'EOD'
            <?php

            declare(strict_types=1);

            // PHPStan Symfony console application loader.
            // Adjust the Kernel class and env to match your application.
            // See https://github.com/phpstan/phpstan-symfony#configuration

            use App\Kernel;
            use Symfony\Bundle\FrameworkBundle\Console\Application;
            use Symfony\Component\Dotenv\Dotenv;

            require __DIR__ . '/../vendor/autoload.php';

            (new Dotenv())->bootEnv(__DIR__ . '/../.env');

            $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

            return new Application($kernel);

            EOD;
    }
}
