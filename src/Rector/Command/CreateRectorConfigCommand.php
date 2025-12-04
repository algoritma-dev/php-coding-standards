<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector\Command;

use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriter;
use Algoritma\CodingStandards\Rector\Writer\RectorConfigWriterInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRectorConfigCommand extends BaseCommand
{
    private RectorConfigWriterInterface $configWriter;

    public function __construct(?string $name = null)
    {
        $this->configWriter = new RectorConfigWriter();

        parent::__construct($name);
    }

    public function getConfigWriter(): RectorConfigWriterInterface
    {
        return $this->configWriter;
    }

    public function setConfigWriter(RectorConfigWriterInterface $configWriter): void
    {
        $this->configWriter = $configWriter;
    }

    protected function configure(): void
    {
        $this
            ->setName('algoritma-rector-create-config')
            ->setDescription('Write the configuration for rector')
            ->setDefinition([
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Do not include autoload-dev directories'),
            ])
            ->setHelp(
                <<<'EOD'
                    Write config file in <comment>rector.php</comment>.
                    EOD,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configWriter = $this->getConfigWriter();

        $configWriter->writeConfigFile(
            'rector.php',
            (bool) $input->getOption('no-dev'),
        );

        $output->writeln('<success>Rector config file created.</success>');

        return 0;
    }
}
