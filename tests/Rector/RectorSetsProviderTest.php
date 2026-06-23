<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector;

use Algoritma\CodingStandards\Rector\RectorSetsProvider;
use Frosh\Rector\Set\ShopwareSetList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RectorSetsProviderTest extends TestCase
{
    /**
     * @param array<string> $expectedSets
     */
    #[DataProvider('shopwareVersionProvider')]
    public function testGetShopwareSets(string $version, array $expectedSets): void
    {
        $provider = new RectorSetsProvider(
            static fn (string $package): ?string => $package === 'shopware/core' ? $version : null
        );
        $sets = $provider->getSets();

        foreach ($expectedSets as $expectedSet) {
            $this->assertContains($expectedSet, $sets);
        }
    }

    /**
     * @return \Generator<string, array{string, array<string>}>
     */
    public static function shopwareVersionProvider(): \Generator
    {
        yield 'Shopware 6.8' => ['6.8.0.0', [ShopwareSetList::SHOPWARE_6_8_0]];
        yield 'Shopware 6.7' => ['6.7.0.0', [ShopwareSetList::SHOPWARE_6_7_0]];
        yield 'Shopware 6.6' => ['6.6.0.0', [ShopwareSetList::SHOPWARE_6_6_10]];
    }
}

namespace Frosh\Rector\Set;

class ShopwareSetList
{
    public const SHOPWARE_6_6_10 = __DIR__ . '/../../config/shopware-6.6.10.php';

    public const SHOPWARE_6_7_0 = __DIR__ . '/../../config/shopware-6.7.0.php';

    public const SHOPWARE_6_8_0 = __DIR__ . '/../../config/shopware-6.8.0.php';
}
