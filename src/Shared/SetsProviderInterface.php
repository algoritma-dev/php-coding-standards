<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Shared;

interface SetsProviderInterface
{
    /**
     * Get rules.
     *
     * @return array<string|int, array<string, mixed>|bool|string>
     */
    public function getSets(): array;
}
