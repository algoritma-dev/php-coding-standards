<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer;

use Algoritma\CodingStandards\Installer\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Installer\Command\CreateRectorConfigCommand;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;
use Algoritma\CodingStandards\Installer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Installer\CommandProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class CommandProviderTest extends TestCase
{
    public function testGetCommands(): void
    {
        $provider = new CommandProvider();

        $this->assertInstanceOf(ComposerCommandProvider::class, $provider);

        $commands = $provider->getCommands();
        $this->assertCount(3, $commands);
        $this->assertInstanceOf(CreatePhpCsFixerConfigCommand::class, $commands[0]);
        $this->assertInstanceOf(CreatePhpstanConfigCommand::class, $commands[1]);
        $this->assertInstanceOf(CreateRectorConfigCommand::class, $commands[2]);
    }
}
