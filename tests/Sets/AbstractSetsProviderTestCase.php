<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Sets;

use Algoritma\CodingStandards\Sets\SetsProviderInterface;
use Algoritma\CodingStandardsTest\Framework\TestCase;

abstract class AbstractSetsProviderTestCase extends TestCase
{
    public function testRulesAreAlphabeticallySorted(): void
    {
        $this->assertRulesAreAlphabeticallySorted(static::getSetsProvider());
    }

    protected static function getSetsProvider(): SetsProviderInterface
    {
        throw new \LogicException(sprintf('Override %s to provide the proper concrete instance of %s', __METHOD__, SetsProviderInterface::class));
    }

    protected function assertRulesAreAlphabeticallySorted(SetsProviderInterface $provider): void
    {
        $sets = $provider->getSets();

        $sortedSets = $sets;
        ksort($sortedSets);

        self::assertEquals($sortedSets, $sets, 'Sets are not alphabetically sorted');
    }
}
