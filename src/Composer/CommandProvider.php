<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Composer;

use Algoritma\CodingStandards\PhpCsFixer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Phpstan\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Rector\Command\CreateRectorConfigCommand;
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
