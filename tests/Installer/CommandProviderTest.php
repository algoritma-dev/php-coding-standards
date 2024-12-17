<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer;

use Algoritma\CodingStandards\Installer\Command\CreatePhpstanConfigCommand;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;
use Algoritma\CodingStandards\Installer\Command\CreateConfigCommand;
use Algoritma\CodingStandards\Installer\CommandProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class CommandProviderTest extends TestCase
{
    public function testGetCommands(): void
    {
        $provider = new CommandProvider();

        $this->assertInstanceOf(ComposerCommandProvider::class, $provider);

        $commands = $provider->getCommands();
        $this->assertCount(2, $commands);
        $this->assertInstanceOf(CreateConfigCommand::class, $commands[0]);
        $this->assertInstanceOf(CreatePhpstanConfigCommand::class, $commands[1]);
    }
}
