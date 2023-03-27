<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rules;

interface RulesProviderInterface
{
    /**
     * Get rules.
     *
     * @return array<string, mixed>
     */
    public function getRules(): array;
}
