<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Phpstan\Command;

use Algoritma\CodingStandards\Phpstan\Writer\PhpstanAlgoritmaConfigWriter;
use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandards\Phpstan\Writer\PhpstanConfigWriterInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePhpstanConfigCommand extends BaseCommand
{
    private PhpstanConfigWriterInterface $configWriter;

    private PhpstanConfigWriterInterface $algoritmaConfigWriter;

    public function __construct(?string $name = null)
    {
        $this->configWriter = new PhpstanConfigWriter();
        $this->algoritmaConfigWriter = new PhpstanAlgoritmaConfigWriter();

        parent::__construct($name);
    }

    public function getConfigWriter(): PhpstanConfigWriterInterface
    {
        return $this->configWriter;
    }

    public function setConfigWriter(PhpstanConfigWriterInterface $configWriter): void
    {
        $this->configWriter = $configWriter;
    }

    public function getAlgoritmaConfigWriter(): PhpstanConfigWriterInterface
    {
        return $this->algoritmaConfigWriter;
    }

    public function setAlgoritmaConfigWriter(PhpstanConfigWriterInterface $algoritmaConfigWriter): void
    {
        $this->algoritmaConfigWriter = $algoritmaConfigWriter;
    }

    protected function configure(): void
    {
        $this
            ->setName('algoritma-phpstan-create-config')
            ->setDescription('Write the configuration for phpstan')
            ->setDefinition([
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Do not include autoload-dev directories'),
            ])
            ->setHelp(
                <<<'EOD'
                    Write config file in <comment>phpstan.neon</comment>.
                    Write config file in <comment>phpstan-algoritma-config.php</comment>.
                    EOD,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configWriter = $this->getConfigWriter();
        $algoritmaConfigWriter = $this->getAlgoritmaConfigWriter();

        $configWriter->writeConfigFile(
            'phpstan.neon',
            (bool) $input->getOption('no-dev'),
        );

        $algoritmaConfigWriter->writeConfigFile(
            'phpstan-algoritma-config.php',
            (bool) $input->getOption('no-dev'),
        );

        $output->writeln('<success>PHPStan config file created.</success>');

        return 0;
    }
}
