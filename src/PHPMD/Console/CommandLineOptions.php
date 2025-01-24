<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\PHPMD\Console;

class CommandLineOptions extends \PHPMD\TextUI\CommandLineOptions
{
    public function __construct(array $args, array $availableRuleSets = [])
    {
        parent::__construct($args, $availableRuleSets);

        $this->ruleSets = dirname(__DIR__) . '/resources/rulesets/algoritma.xml,' . $this->ruleSets;
    }
}
