<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Rector;

use Algoritma\CodingStandards\Rector\RectorSetsProvider;
use Composer\InstalledVersions;
use FriendsOfShopware\ShopwareRector\Set\ShopwareSetList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rector\PHPUnit\Set\PHPUnitSetList;

use function is_string;

class RectorSetsProviderTest extends TestCase
{
    /**
     * @var array{root: array<mixed>, versions: array<mixed>}
     */
    private static array $installedVersionsData;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$installedVersionsData = InstalledVersions::getAllRawData()[0];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        InstalledVersions::reload(self::$installedVersionsData);
    }

    /**
     * @param array<string> $expectedSets
     */
    #[DataProvider('phpUnitVersionProvider')]
    public function testGetPhpUnitSets(string $version, array $expectedSets): void
    {
        $this->mockInstalledVersions('phpunit/phpunit', $version);

        $provider = new RectorSetsProvider();
        $sets = $provider->getSets();

        foreach ($expectedSets as $expectedSet) {
            $this->assertContains($expectedSet, $sets);
        }
    }

    /**
     * @param array<string> $expectedSets
     */
    #[DataProvider('shopwareVersionProvider')]
    public function testGetShopwareSets(string $version, array $expectedSets): void
    {
        $this->mockInstalledVersions('shopware/core', $version);

        $provider = new RectorSetsProvider();
        $sets = $provider->getSets();

        foreach ($expectedSets as $expectedSet) {
            $this->assertContains($expectedSet, $sets);
        }
    }

    /**
     * @return \Generator<string, array{string, array<string>}>
     */
    public static function phpUnitVersionProvider(): \Generator
    {
        yield 'PHPUnit 12' => ['12.0.0', [PHPUnitSetList::PHPUNIT_120]];
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

    /**
     * @param array<string, string|null>|string $packages
     */
    private function mockInstalledVersions(array|string $packages, ?string $version = null): void
    {
        $versions = [];
        if (is_string($packages)) {
            $packages = [$packages => $version];
        }

        foreach ($packages as $package => $ver) {
            $versions[$package] = [
                'pretty_version' => $ver,
                'version' => $ver,
                'aliases' => [],
                'reference' => 'mock',
                'dev_requirement' => true,
            ];
        }

        $data = [
            'root' => [
                'name' => 'algoritma/php-coding-standard',
                'pretty_version' => '1.0.0',
                'version' => '1.0.0',
                'reference' => 'mock',
                'type' => 'library',
                'install_path' => __DIR__ . '/../../..',
                'aliases' => [],
                'dev' => true,
            ],
            'versions' => $versions,
        ];

        InstalledVersions::reload($data);
    }
}

namespace FriendsOfShopware\ShopwareRector\Set;

class ShopwareSetList
{
    public const SHOPWARE_6_6_10 = __DIR__ . '/../../config/shopware-6.6.10.php';

    public const SHOPWARE_6_7_0 = __DIR__ . '/../../config/shopware-6.7.0.php';

    public const SHOPWARE_6_8_0 = __DIR__ . '/../../config/shopware-6.8.0.php';
}
