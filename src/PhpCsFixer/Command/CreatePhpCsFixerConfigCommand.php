<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\PhpCsFixer\Command;

use Algoritma\CodingStandards\PhpCsFixer\Writer\PhpCsConfigFixerWriter;
use Algoritma\CodingStandards\PhpCsFixer\Writer\PhpCsConfigWriterInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePhpCsFixerConfigCommand extends BaseCommand
{
    private PhpCsConfigWriterInterface $configWriter;

    public function __construct(?string $name = null)
    {
        $this->configWriter = new PhpCsConfigFixerWriter();

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
            ->setName('algoritma-cs-create-config')
            ->setDescription('Write the algoritma-coding-standard configuration for php-cs-fixer')
            ->setDefinition([
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Do not include autoload-dev directories'),
                new InputOption('no-risky', null, InputOption::VALUE_NONE, 'Do not include risky rules'),
            ])
            ->setHelp(
                <<<'EOD'
                    Write config file in <comment>.php-cs-fixer.dist.php</comment>.
                    EOD,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configWriter = $this->getConfigWriter();

        $configWriter->writeConfigFile(
            '.php-cs-fixer.dist.php',
            (bool) $input->getOption('no-dev'),
            (bool) $input->getOption('no-risky'),
        );

        return 0;
    }
}
