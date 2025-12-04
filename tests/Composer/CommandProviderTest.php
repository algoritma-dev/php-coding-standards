<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Composer;

use Algoritma\CodingStandards\Composer\CommandProvider;
use Algoritma\CodingStandards\PhpCsFixer\Command\CreatePhpCsFixerConfigCommand;
use Algoritma\CodingStandards\Phpstan\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Rector\Command\CreateRectorConfigCommand;
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
