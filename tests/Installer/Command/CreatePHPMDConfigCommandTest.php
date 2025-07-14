<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Installer\Command;

use Algoritma\CodingStandards\Installer\Command\CreatePHPMDConfigCommand;
use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigWriterInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePHPMDConfigCommandTest extends TestCase
{
    public function testGetConfigWriter(): void
    {
        $command = new CreatePHPMDConfigCommand();
        $writer = $command->getConfigWriter();
        static::assertSame($writer, $command->getConfigWriter());
    }

    public function testSetConfigWriter(): void
    {
        $command = new CreatePHPMDConfigCommand();
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        static::assertSame($writer->reveal(), $command->getConfigWriter());
    }

    /**
     * @param list<string> $args
     *
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $command = new CreatePHPMDConfigCommand();
        $writer = $this->prophesize(PhpCsConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());

        $input = new ArgvInput([
            false,
        ], $command->getDefinition());
        $output = $this->prophesize(OutputInterface::class);

        $writer->writeConfigFile('phpmd.xml', false)
            ->shouldBeCalled();

        $result = $command->run($input, $output->reveal());

        static::assertSame(0, $result);
    }
}
