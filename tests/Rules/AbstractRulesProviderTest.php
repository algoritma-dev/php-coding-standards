<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rules;

use Algoritma\CodingStandards\Rules\RulesProviderInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;

abstract class AbstractRulesProviderTest extends TestCase
{
    public function testRulesAreAlphabeticallySorted(): void
    {
        $this->assertRulesAreAlphabeticallySorted(static::getRulesProvider());
    }

    abstract protected function shouldBeRisky(): bool;

    protected static function getRulesProvider(): RulesProviderInterface
    {
        throw new \LogicException(sprintf('Override %s to provide the proper concrete instance of %s', __METHOD__, RulesProviderInterface::class));
    }

    protected function assertRulesAreAlphabeticallySorted(RulesProviderInterface $provider): void
    {
        $rules = $provider->getRules();

        $sortedRules = $rules;
        ksort($sortedRules);

        static::assertEquals($sortedRules, $rules, 'Rules are not alphabetically sorted');
    }
}
