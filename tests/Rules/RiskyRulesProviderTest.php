<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\PhpCsFixer\Rules\RiskyRulesProvider;
use Algoritma\CodingStandards\Shared\Rules\RulesProviderInterface;

class RiskyRulesProviderTest extends AbstractRulesProviderTestCase
{
    protected static function getRulesProvider(): RulesProviderInterface
    {
        return new RiskyRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return true;
    }
}
