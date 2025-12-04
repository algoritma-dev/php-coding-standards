<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Shared\Rules;

interface RulesProviderInterface
{
    /**
     * Get rules.
     *
     * @return array<string|int, array<string, mixed>|bool|string>
     */
    public function getRules(): array;
}
