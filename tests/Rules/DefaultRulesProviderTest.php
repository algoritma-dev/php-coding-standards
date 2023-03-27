<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\DefaultRulesProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class DefaultRulesProviderTest extends TestCase
{
    public function testGetRules(): void
    {
        $provider = new DefaultRulesProvider();

        $this->assertIsArray($provider->getRules());
    }
}
