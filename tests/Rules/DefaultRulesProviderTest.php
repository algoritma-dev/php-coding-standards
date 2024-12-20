<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\DefaultRulesProvider;

class DefaultRulesProviderTest extends AbstractRulesProviderTestCase
{
    protected static function getRulesProvider(): DefaultRulesProvider
    {
        return new DefaultRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return \false;
    }
}
