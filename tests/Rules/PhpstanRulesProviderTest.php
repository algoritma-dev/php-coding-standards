<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Phpstan\Rules\PhpstanRulesProvider;

class PhpstanRulesProviderTest extends AbstractRulesProviderTestCase
{
    protected static function getRulesProvider(): PhpstanRulesProvider
    {
        return new PhpstanRulesProvider();
    }

    protected function shouldBeRisky(): bool
    {
        return \false;
    }
}
