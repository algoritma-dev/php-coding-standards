<?php

namespace Algoritma\CodingStandards;

class ProjectAwareConfigurationProvider
{
    public const PHPSTAN_CONFIG = 'phpstan';

    public const RECTOR_CONFIG = 'rector';

    private const PATHS_NEED_PROJECT_ROOT = [
        'paths',
        'bootstrapFiles',
        'tmpDir',
        'excludePaths'
    ];

    /**
     * @var string
     */
    private $composerPath;

    /**
     * @var string
     */
    private $projectRoot;

    public function __construct(?string $composerFile = null, ?string $projectRoot = null)
    {
        $this->composerPath = $composerFile ?: trim(getenv('COMPOSER') ?: '') ?: './composer.json';

        $projectRootPath = $projectRoot ?: realpath(\dirname($this->composerPath));

        if (false === $projectRootPath) {
            throw new \RuntimeException('Unable to get project root.');
        }

        $this->projectRoot = rtrim($projectRootPath, '/\\');
    }

    public function getConfiguration(string $configType): array
    {
        if (!file_exists($this->composerPath)) {
            throw new \RuntimeException('Unable to find composer.json');
        }

        switch ($configType) {
            case self::PHPSTAN_CONFIG:
                if (file_exists($this->projectRoot . '/phpstan.php')) {
                    $configuration = require $this->projectRoot . '/phpstan.php';

                    foreach (self::PATHS_NEED_PROJECT_ROOT as $path) {
                        if (isset($configuration['parameters'][$path])) {
                            $configuration['parameters'][$path] = $this->concatenateProjectRoot($configuration['parameters'][$path]);
                        }
                    }

                    return $configuration;
                }
                break;
            case self::RECTOR_CONFIG:
                if (file_exists($this->projectRoot . '/rector.php')) {
                    return require $this->projectRoot . '/rector.php';
                }
                break;
        }

        return [];
    }

    private function concatenateProjectRoot($paths)
    {
        if (is_string($paths) && !str_starts_with($paths, $this->projectRoot)) {
            return $this->projectRoot . \DIRECTORY_SEPARATOR . $paths;
        }

        if(is_array($paths)) {
            return array_map(function ($path) {
                return $this->concatenateProjectRoot($path);
            }, $paths);
        }

        return $paths;
    }
}