<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\CompositeRulesProvider;
use Algoritma\CodingStandards\Rules\RulesProviderInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;

class CompositeRulesProviderTest extends TestCase
{
    public function testGetRules(): void
    {
        $provider1 = $this->prophesize(RulesProviderInterface::class);
        $provider2 = $this->prophesize(RulesProviderInterface::class);

        $provider1->getRules()->willReturn([
            'foo' => true,
            'bar' => ['opt' => true],
            'another' => true,
        ]);

        $provider2->getRules()->willReturn([
            'foo' => true,
            'bar' => false,
            'dummy' => ['opt2' => false],
        ]);

        $provider = new CompositeRulesProvider([
            $provider1->reveal(),
            $provider2->reveal(),
        ]);

        $expected = [
            'foo' => true,
            'bar' => false,
            'another' => true,
            'dummy' => ['opt2' => false],
        ];

        self::assertSame($expected, $provider->getRules());
    }
}
