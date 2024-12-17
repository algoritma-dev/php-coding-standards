<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer;

use Algoritma\CodingStandards\Installer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Installer\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Installer\Command\CreateRectorConfigCommand;
use Composer\Command\BaseCommand;

class CommandProvider implements \Composer\Plugin\Capability\CommandProvider
{
    /**
     * Retrieves an array of commands.
     *
     * @return BaseCommand[]
     */
    public function getCommands(): array
    {
        return [
            new CreatePhpCsFixerConfigCommand(),
            new CreatePhpstanConfigCommand(),
            new CreateRectorConfigCommand(),
        ];
    }
}
