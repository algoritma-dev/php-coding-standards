<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer\Command;

use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigWriterInterface;
use Algoritma\CodingStandards\Installer\Writer\PHPMDConfigWriter;
use Algoritma\CodingStandards\Installer\Writer\PhpstanConfigWriter;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePHPMDConfigCommand extends BaseCommand
{
    private PhpCsConfigWriterInterface $configWriter;

    public function __construct(?string $name = null)
    {
        $this->configWriter = new PHPMDConfigWriter();

        parent::__construct($name);
    }

    public function getConfigWriter(): PhpCsConfigWriterInterface
    {
        return $this->configWriter;
    }

    public function setConfigWriter(PhpCsConfigWriterInterface $configWriter): void
    {
        $this->configWriter = $configWriter;
    }

    protected function configure(): void
    {
        $this
            ->setName('algoritma-phpmd-create-config')
            ->setDescription('Write the configuration for PHPMD')
            ->setDefinition([
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Do not include autoload-dev directories'),
            ])
            ->setHelp(
                <<<'EOD'
                    Write config file in <comment>phpmd.xml</comment>.
                    EOD,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configWriter = $this->getConfigWriter();

        $configWriter->writeConfigFile(
            'phpmd.xml',
            (bool) $input->getOption('no-dev'),
        );

        return 0;
    }
}
