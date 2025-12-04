<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Phpstan\Command;

use Algoritma\CodingStandards\Phpstan\Command\CreatePhpstanConfigCommand;
use Algoritma\CodingStandards\Phpstan\Writer\PhpstanAlgoritmaConfigWriter;
use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriterInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePhpstanConfigCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        putenv('COMPOSER_HOME=' . sys_get_temp_dir());
    }

    public function testConstructor(): void
    {
        $command = new CreatePhpstanConfigCommand();

        $writer = $command->getConfigWriter();
        $algoritmaWriter = $command->getAlgoritmaConfigWriter();

        self::assertInstanceOf(PhpstanConfigWriter::class, $writer);
        self::assertInstanceOf(PhpstanAlgoritmaConfigWriter::class, $algoritmaWriter);

        // Ensure getters return the same instance.
        self::assertSame($writer, $command->getConfigWriter());
        self::assertSame($algoritmaWriter, $command->getAlgoritmaConfigWriter());
    }

    public function testConfigure(): void
    {
        $command = new CreatePhpstanConfigCommand();

        self::assertSame('algoritma-phpstan-create-config', $command->getName());
        self::assertSame('Write the configuration for phpstan', $command->getDescription());
        self::assertTrue($command->getDefinition()->hasOption('no-dev'));

        $option = $command->getDefinition()->getOption('no-dev');
        self::assertFalse($option->acceptValue());
        self::assertSame('Do not include autoload-dev directories', $option->getDescription());

        $expectedHelp = <<<'EOD'
            Write config file in <comment>phpstan.neon</comment>.
            Write config file in <comment>phpstan-algoritma-config.php</comment>.
            EOD;
        self::assertSame($expectedHelp, $command->getHelp());
    }

    public function testSetConfigWriter(): void
    {
        $command = new CreatePhpstanConfigCommand();
        $writer = $this->prophesize(PhpstanConfigWriterInterface::class);
        $algoritmaWriter = $this->prophesize(PhpstanConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        $command->setAlgoritmaConfigWriter($algoritmaWriter->reveal());
        self::assertSame($writer->reveal(), $command->getConfigWriter());
        self::assertSame($algoritmaWriter->reveal(), $command->getAlgoritmaConfigWriter());
    }

    /**
     * @param list<string> $args
     *
     * @throws \Exception
     */
    #[DataProvider('executeProvider')]
    public function testExecute(array $args, bool $noDev): void
    {
        $command = new CreatePhpstanConfigCommand();
        $writer = $this->prophesize(PhpstanConfigWriterInterface::class);
        $algoritmaWriter = $this->prophesize(PhpstanConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        $command->setAlgoritmaConfigWriter($algoritmaWriter->reveal());

        $input = new ArgvInput($args, $command->getDefinition());
        $output = $this->prophesize(OutputInterface::class);

        $writer->writeConfigFile(
            'phpstan.neon',
            $noDev,
        )
            ->shouldBeCalled();

        $algoritmaWriter->writeConfigFile(
            'phpstan-algoritma-config.php',
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
                ['algoritma-phpstan-create-config'],
                false,
            ],
            [
                ['algoritma-phpstan-create-config', '--no-dev'],
                true,
            ],
        ];
    }

    /**
     * @param list<string> $args
     * @param class-string<\Throwable> $expectedException
     */
    #[DataProvider('executeThrowsExceptionProvider')]
    public function testExecuteThrowsException(
        array $args,
        bool $noDev,
        bool $firstWriterThrows,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $command = new CreatePhpstanConfigCommand();
        $writer = $this->prophesize(PhpstanConfigWriterInterface::class);
        $algoritmaWriter = $this->prophesize(PhpstanConfigWriterInterface::class);
        $command->setConfigWriter($writer->reveal());
        $command->setAlgoritmaConfigWriter($algoritmaWriter->reveal());

        $input = new ArgvInput($args, $command->getDefinition());
        $output = $this->prophesize(OutputInterface::class);

        $exception = new $expectedException($expectedExceptionMessage);

        if ($firstWriterThrows) {
            $writer->writeConfigFile('phpstan.neon', $noDev)
                ->shouldBeCalled()
                ->willThrow($exception);

            $algoritmaWriter->writeConfigFile('phpstan-algoritma-config.php', $noDev)
                ->shouldNotBeCalled();
        } else {
            $writer->writeConfigFile('phpstan.neon', $noDev)
                ->shouldBeCalled();

            $algoritmaWriter->writeConfigFile('phpstan-algoritma-config.php', $noDev)
                ->shouldBeCalled()
                ->willThrow($exception);
        }

        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $command->run($input, $output->reveal());
    }

    /**
     * @return array{list<string>, bool, bool, class-string<\Throwable>, string}[]
     */
    public static function executeThrowsExceptionProvider(): array
    {
        $args = ['algoritma-phpstan-create-config'];
        $argsNoDev = ['algoritma-phpstan-create-config', '--no-dev'];

        return [
            'first writer throws' => [
                $args,
                false,
                true,
                \RuntimeException::class,
                'Failed to write config file',
            ],
            'second writer throws' => [
                $args,
                false,
                false,
                \InvalidArgumentException::class,
                'Some other error',
            ],
            'first writer throws with no-dev' => [
                $argsNoDev,
                true,
                true,
                \RuntimeException::class,
                'Failed to write config file',
            ],
            'second writer throws with no-dev' => [
                $argsNoDev,
                true,
                false,
                \InvalidArgumentException::class,
                'Some other error',
            ],
        ];
    }
}
