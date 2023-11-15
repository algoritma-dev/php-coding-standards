<?php

namespace Algoritma\CodingStandards\Provider;

class ProjectRootProvider
{
    private string $composerPath;

    private string $projectRoot;

    public function __construct($composerFile = null)
    {
        $this->composerPath = $composerFile ?: trim(getenv('COMPOSER') ?: '') ?: './composer.json';

        $projectRootPath = realpath(\dirname($this->composerPath));

        if (false === $projectRootPath) {
            throw new \RuntimeException('Unable to get project root.');
        }

        $this->projectRoot = rtrim($projectRootPath, '/\\');
    }

    public function getProjectRoot()
    {
        return $this->projectRoot;
    }

    public function getComposerPath()
    {
        return $this->composerPath;
    }
}