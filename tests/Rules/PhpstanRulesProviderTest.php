<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\PhpstanRulesProvider;

use const false;

class PhpstanRulesProviderTest extends AbstractRulesProviderTest
{
    protected static function getRulesProvider(): PhpstanRulesProvider
    {
        return new PhpstanRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return false;
    }
}
