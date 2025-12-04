<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector\Command;

use Algoritma\CodingStandards\Rector\Command\CreateRectorConfigCommand;
use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriterInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRectorConfigCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        putenv('COMPOSER_HOME=' . sys_get_temp_dir());
    }

    public function testGetConfigWriter(): void
    {
        $command = new CreateRectorConfigCommand();
        $writer = $command->getConfigWriter();
        self::assertSame($writer, $command->getConfigWriter());
    }

    public function testSetConfigWriter(): void
    {
        $command = new CreateRectorConfigCommand();
        $writer = $this->prophesize(RectorConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        self::assertSame($writer->reveal(), $command->getConfigWriter());
    }

    /**
     * @param list<string> $args
     *
     * @throws \Exception
     */
    #[DataProvider('executeProvider')]
    public function testExecute(array $args, bool $noDev): void
    {
        $command = new CreateRectorConfigCommand();
        $writer = $this->prophesize(RectorConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());

        $input = new ArgvInput($args, $command->getDefinition());
        $output = $this->prophesize(OutputInterface::class);

        $writer->writeConfigFile(
            'rector.php',
            $noDev,
        )
            ->shouldBeCalled();

        $result = $command->run($input, $output->reveal());

        self::assertSame(0, $result);
    }

    /**
     * @return array{string[], bool}[]
     */
    public static function executeProvider(): array
    {
        return [
            [
                ['algoritma-rector-create-config'],
                false,
            ],
            [
                ['algoritma-rector-create-config', '--no-dev'],
                true,
            ],
        ];
    }
}
