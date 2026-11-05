<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest\Framework;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class AbstractTestCase extends PHPUnitTestCase
{
    use ProphecyTrait;
}
