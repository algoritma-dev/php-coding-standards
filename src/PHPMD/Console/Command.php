<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\PHPMD\Console;

use PHPMD\Console\OutputInterface;
use PHPMD\Console\StreamOutput;
use PHPMD\RuleSetFactory;
use PHPMD\Writer\StreamWriter;

class Command extends \PHPMD\TextUI\Command
{
    public static function main(array $args)
    {
        $options = null;
        try {
            $ruleSetFactory = new RuleSetFactory();
            $options        = new CommandLineOptions($args, $ruleSetFactory->listAvailableRuleSets());
            $errorFile      = $options->getErrorFile();
            $errorStream    = new StreamWriter($errorFile ?: STDERR);
            $output         = new StreamOutput($errorStream->getStream(), $options->getVerbosity());
            $command        = new self($output);

            foreach ($options->getDeprecations() as $deprecation) {
                $output->write($deprecation . PHP_EOL . PHP_EOL);
            }

            $exitCode = $command->run($options, $ruleSetFactory);
            unset($errorStream);
        } catch (\Exception $e) {
            $file = $options instanceof CommandLineOptions ? $options->getErrorFile() : null;
            $writer = new StreamWriter($file ?: STDERR);
            $writer->write($e->getMessage() . PHP_EOL);

            if ($options && $options->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
                $writer->write($e->getFile() . ':' . $e->getLine() . PHP_EOL);
                $writer->write($e->getTraceAsString() . PHP_EOL);
            }

            $exitCode = self::EXIT_EXCEPTION;
        }

        return $exitCode;
    }
}
