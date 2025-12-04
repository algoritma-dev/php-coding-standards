<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Composer;

use Algoritma\CodingStandards\PhpCsFixer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Phpstan\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Rector\Command\CreateRectorConfigCommand;
use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;

class CommandProvider implements ComposerCommandProvider
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
