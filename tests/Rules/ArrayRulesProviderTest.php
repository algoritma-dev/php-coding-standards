<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\ArrayRulesProvider;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class ArrayRulesProviderTest extends TestCase
{
    public function testGetRules(): void
    {
        $rules = ['foo' => true, 'bar' => ['baz' => 'foobar']];

        $provider = new ArrayRulesProvider($rules);

        static::assertSame($rules, $provider->getRules());
    }
}
