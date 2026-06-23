<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rector;

use Algoritma\CodingStandards\Shared\SetsProviderInterface;
use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Frosh\Rector\Set\ShopwareSetList;
use Rector\Php\PhpVersionResolver\ComposerJsonPhpVersionResolver;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;

class RectorSetsProvider implements SetsProviderInterface
{
    /**
     * @var callable(string): ?string
     */
    private $versionResolver;

    /**
     * @param (callable(string): ?string)|null $versionResolver resolves the installed version of a Composer package, or null when absent
     */
    public function __construct(?callable $versionResolver = null)
    {
        $this->versionResolver = $versionResolver ?? static function (string $package): ?string {
            if (! class_exists(InstalledVersions::class) || ! InstalledVersions::isInstalled($package)) {
                return null;
            }

            return InstalledVersions::getVersion($package);
        };
    }

    /**
     * @return array<string>
     */
    public function getSets(): array
    {
        $sets = [
            SetList::CODE_QUALITY,
            SetList::EARLY_RETURN,
            SetList::INSTANCEOF,
            SetList::DEAD_CODE,
            $this->getPhpSet(),
            SetList::TYPE_DECLARATION,
        ];

        return array_merge(
            $sets,
            $this->getPhpUnitSets(),
            $this->getShopwareSets()
        );
    }

    private function getPhpSet(): string
    {
        $version = ComposerJsonPhpVersionResolver::resolveFromCwdOrFail();

        return match (true) {
            $version >= 80100 && $version <= 80199 => SetList::PHP_81,
            $version >= 80200 && $version <= 80299 => SetList::PHP_82,
            $version >= 80300 && $version <= 80399 => SetList::PHP_83,
            $version >= 80400 && $version <= 80499 => SetList::PHP_84,
            $version >= 80500 && $version <= 80599 => SetList::PHP_85,
            true => throw new \Exception('PHP version not supported'),
        };
    }

    /**
     * @return array<string>
     */
    private function getPhpUnitSets(): array
    {
        $version = ($this->versionResolver)('phpunit/phpunit');
        if ($version === null) {
            return [];
        }

        $versionParser = new VersionParser();
        $phpunitVersion = $versionParser->normalize($version);

        if (str_starts_with($phpunitVersion, '12.')) {
            return [PHPUnitSetList::PHPUNIT_120];
        }

        if (str_starts_with($phpunitVersion, '11.')) {
            return [PHPUnitSetList::PHPUNIT_110];
        }

        if (str_starts_with($phpunitVersion, '10.')) {
            return [PHPUnitSetList::PHPUNIT_100];
        }

        if (str_starts_with($phpunitVersion, '9.')) {
            return [PHPUnitSetList::PHPUNIT_90];
        }

        return [];
    }

    /**
     * @return array<string>
     */
    private function getShopwareSets(): array
    {
        $version = ($this->versionResolver)('shopware/core');
        if ($version === null) {
            return [];
        }

        if (! class_exists(ShopwareSetList::class)) {
            throw new \Exception('Shopware Rector is not installed: composer req frosh/shopware-rector --dev');
        }

        $versionParser = new VersionParser();
        $shopwareVersion = $versionParser->normalize($version);

        if (str_starts_with($shopwareVersion, '6.8.')) {
            return [ShopwareSetList::SHOPWARE_6_8_0];
        }

        if (str_starts_with($shopwareVersion, '6.7.')) {
            return [ShopwareSetList::SHOPWARE_6_7_0];
        }

        if (str_starts_with($shopwareVersion, '6.6.')) {
            return [ShopwareSetList::SHOPWARE_6_6_10];
        }

        return [];
    }
}
