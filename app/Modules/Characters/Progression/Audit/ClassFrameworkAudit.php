<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Audit;

defined('ABSPATH') || exit;

/**
 * Stable read model used by Phase III.12 to reason about class completion.
 */
final class ClassFrameworkAudit
{
    public function __construct(
        private ?ClassCapabilityCatalogue $classes = null
    ) {
        $this->classes ??=
            new ClassCapabilityCatalogue();
    }

    /**
     * @return array{
     *     registered:int,
     *     specialist:int,
     *     foundation:int,
     *     classes:array<int,array<string,mixed>>
     * }
     */
    public function report(): array
    {
        $all = $this->classes->all();
        $specialist = $this->classes->specialist();
        $foundation = $this->classes->foundation();

        return [
            'registered' => count($all),
            'specialist' => count($specialist),
            'foundation' => count($foundation),
            'classes' => array_map(
                static fn (
                    ClassCapabilityProfile $profile
                ): array =>
                    $profile->toArray(),
                $all
            ),
        ];
    }
}
