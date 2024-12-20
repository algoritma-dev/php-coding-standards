<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\RectorRulesProvider;

class RectorRulesProviderTest extends AbstractRulesProviderTestCase
{
    protected static function getRulesProvider(): RectorRulesProvider
    {
        return new RectorRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return \false;
    }
}
