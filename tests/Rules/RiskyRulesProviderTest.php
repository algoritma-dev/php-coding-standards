<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\RiskyRulesProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class RiskyRulesProviderTest extends TestCase
{
    public function testGetRules(): void
    {
        $provider = new RiskyRulesProvider();

        $this->assertIsArray($provider->getRules());
    }
}
