<?php

namespace Algoritma\CodingStandards\Composer;

use Composer\Command\BaseCommand;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddComposerScripts extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('algoritma-add-composer-scripts')
            ->setDescription('Write the algoritma-coding-standard configuration for php-cs-fixer')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $installer = new Installer($io);
        $installer->requestAddComposerScripts();

        return 0;
    }
}