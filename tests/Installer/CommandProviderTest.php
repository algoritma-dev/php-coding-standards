<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer;

use Algoritma\CodingStandards\Installer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Installer\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Installer\Command\CreateRectorConfigCommand;
use Algoritma\CodingStandards\Installer\CommandProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;

class CommandProviderTest extends TestCase
{
    public function testGetCommands(): void
    {
        $provider = new CommandProvider();

        static::assertInstanceOf(ComposerCommandProvider::class, $provider);

        $commands = $provider->getCommands();
        static::assertCount(3, $commands);
        static::assertInstanceOf(CreatePhpCsFixerConfigCommand::class, $commands[0]);
        static::assertInstanceOf(CreatePhpstanConfigCommand::class, $commands[1]);
        static::assertInstanceOf(CreateRectorConfigCommand::class, $commands[2]);
    }
}
