<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\RectorRulesProvider;

use const false;

class RectorRulesProviderTest extends AbstractRulesProviderTest
{
    protected static function getRulesProvider(): RectorRulesProvider
    {
        return new RectorRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return false;
    }
}
