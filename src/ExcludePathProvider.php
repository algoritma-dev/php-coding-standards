<?php

namespace Algoritma\CodingStandards;

class ExcludePathProvider
{
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

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        if (! file_exists($this->composerPath)) {
            throw new \RuntimeException('Unable to find composer.json');
        }

        if (file_exists($this->projectRoot . '/excl_paths.php')) {
            $paths = require $this->projectRoot . '/excl_paths.php';

            return $this->concatenateProjectRoot($paths);
        }

        return [];
    }

    private function concatenateProjectRoot($paths)
    {
        if (!is_array($paths)) {
            return $this->projectRoot . \DIRECTORY_SEPARATOR . $paths;
        }

        return array_map(function (string $path): string {
            return $this->concatenateProjectRoot($path);
        }, $paths);
    }
}