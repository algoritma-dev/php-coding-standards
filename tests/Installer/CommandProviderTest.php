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

        self::assertInstanceOf(ComposerCommandProvider::class, $provider);

        $commands = $provider->getCommands();
        self::assertCount(3, $commands);
        self::assertInstanceOf(CreatePhpCsFixerConfigCommand::class, $commands[0]);
        self::assertInstanceOf(CreatePhpstanConfigCommand::class, $commands[1]);
        self::assertInstanceOf(CreateRectorConfigCommand::class, $commands[2]);
    }
}
