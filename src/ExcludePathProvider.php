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

    /**
     * AutoloadPathProvider constructor.
     *
     * @param null|string $composerFile
     * @param null|string $projectRoot
     * @param bool $dev
     */
    public function __construct(?string $composerFile = null, ?string $projectRoot = null, bool $dev = true)
    {
        $this->composerPath = $composerFile ?: trim(getenv('COMPOSER') ?: '') ?: './composer.json';

        $projectRootPath = $projectRoot ?: realpath(\dirname($this->composerPath));

        if (false === $projectRootPath) {
            throw new \RuntimeException('Unable to get project root.');
        }

        $this->projectRoot = rtrim($projectRootPath, '/\\');
        $this->dev = $dev;
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
            return require $this->projectRoot . '/excl_paths.php';
        }

        return [];
    }
}