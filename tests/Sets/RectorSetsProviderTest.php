<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Sets;

use Algoritma\CodingStandards\Sets\RectorSetsProvider;

class RectorSetsProviderTest extends AbstractSetsProviderTest
{
    protected static function getSetsProvider(): RectorSetsProvider
    {
        return new RectorSetsProvider();
    }
}
