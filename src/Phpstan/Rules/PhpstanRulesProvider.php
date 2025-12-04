<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Phpstan\Rules;

use Algoritma\CodingStandards\Shared\Rules\RulesProviderInterface;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule;
use Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule;
use Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule;
use Symplify\PHPStanRules\Rules\NoDynamicNameRule;
use Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule;
use Symplify\PHPStanRules\Rules\UppercaseConstantRule;

final class PhpstanRulesProvider implements RulesProviderInterface
{
    public function getRules(): array
    {
        return [
            RequireUniqueEnumConstantRule::class,
            PreventParentMethodVisibilityOverrideRule::class,
            ForbiddenMultipleClassLikeInOneFileRule::class,
            ForbiddenArrayMethodCallRule::class,
            NoDynamicNameRule::class,
            UppercaseConstantRule::class,
        ];
    }
}
