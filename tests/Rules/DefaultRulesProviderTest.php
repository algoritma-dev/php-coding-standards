<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\DefaultRulesProvider;

use const false;

class DefaultRulesProviderTest extends AbstractRulesProviderTest
{
    protected static function getRulesProvider(): DefaultRulesProvider
    {
        return new DefaultRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return false;
    }
}
