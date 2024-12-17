<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Command;

use Algoritma\CodingStandards\Installer\Command\CreatePhpstanConfigCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigWriterInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePhpstanConfigCommandTest extends TestCase
{
    public function testGetConfigWriter(): void
    {
        $command = new CreatePhpstanConfigCommand();
        $writer = $command->getConfigWriter();
        $this->assertSame($writer, $command->getConfigWriter());
    }

    public function testSetConfigWriter(): void
    {
        $command = new CreatePhpstanConfigCommand();
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        $this->assertSame($writer->reveal(), $command->getConfigWriter());
    }

    /**
     * @param list<string> $args
     *
     * @throws \Exception
     */
    #[DataProvider('executeProvider')]
    public function testExecute(array $args, bool $noDev, bool $noRisky): void
    {
        $command = new CreatePhpstanConfigCommand();
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());

        $input = new ArgvInput($args, $command->getDefinition());
        $output = $this->prophesize(OutputInterface::class);

        $writer->writeConfigFile(
            'phpstan.neon',
            $noDev,
            $noRisky,
        )
            ->shouldBeCalled();

        $result = $command->run($input, $output->reveal());

        $this->assertSame(0, $result);
    }

    /**
     * @return array{string[], bool, bool}[]
     */
    public static function executeProvider(): array
    {
        return [
            [
                ['algoritma-phpstan-create-config'],
                false,
                false,
            ],
            [
                ['algoritma-phpstan-create-config', '--no-dev'],
                true,
                false,
            ],
            [
                ['algoritma-phpstan-create-config', '--no-risky'],
                false,
                true,
            ],
            [
                ['algoritma-phpstan-create-config', '--no-dev', '--no-risky'],
                true,
                true,
            ],
        ];
    }
}
