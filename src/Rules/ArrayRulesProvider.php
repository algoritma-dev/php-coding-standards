<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Rules;

final readonly class ArrayRulesProvider implements RulesProviderInterface
{
    /**
     * @param array<string, array<string, mixed>|bool> $rules
     */
    public function __construct(private array $rules)
    {
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
