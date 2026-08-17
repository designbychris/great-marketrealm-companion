<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;

defined('ABSPATH') || exit;

/**
 * Resolves Diceworks formulas from PHP-owned spell/feature scaling rules.
 */
final class ArcaneRollScalingResolver
{
    /**
     * @return array{
     *     formula:?string,
     *     base_formula:?string,
     *     source:string,
     *     resolved_at:?int,
     *     scalable:bool,
     *     slot_options:array<int,string>
     * }
     */
    public function resolve(
        ArcaneAbilityDefinition $ability,
        int $characterLevel,
        ?int $slotLevel = null,
        ?int $featureRank = null
    ): array {
        $baseFormula = $ability->formula();
        $formula = $baseFormula;
        $source = 'base';
        $resolvedAt = null;

        $axes = [
            [
                'source' => 'character-level',
                'value' => max(1, $characterLevel),
                'rules' => $ability->characterLevelScaling(),
            ],
            [
                'source' => 'slot-level',
                'value' => $slotLevel,
                'rules' => $ability->slotLevelScaling(),
            ],
            [
                'source' => 'feature-rank',
                'value' => $featureRank,
                'rules' => $ability->featureRankScaling(),
            ],
        ];

        foreach ($axes as $axis) {
            if (
                $axis['value'] === null
                || $axis['rules'] === []
            ) {
                continue;
            }

            $resolved = $this->formulaAt(
                $axis['rules'],
                (int) $axis['value']
            );

            if ($resolved === null) {
                continue;
            }

            $formula = $resolved['formula'];
            $source = $axis['source'];
            $resolvedAt = $resolved['threshold'];
        }

        return [
            'formula' => $formula,
            'base_formula' => $baseFormula,
            'source' => $source,
            'resolved_at' => $resolvedAt,
            'scalable' =>
                $ability->characterLevelScaling() !== []
                || $ability->slotLevelScaling() !== []
                || $ability->featureRankScaling() !== [],
            'slot_options' => $this->normaliseRules(
                $ability->slotLevelScaling()
            ),
        ];
    }

    /**
     * @param array<int,string> $rules
     * @return array{formula:string,threshold:int}|null
     */
    private function formulaAt(array $rules, int $value): ?array
    {
        $rules = $this->normaliseRules($rules);
        $formula = null;
        $threshold = null;

        foreach ($rules as $minimum => $candidate) {
            if ($minimum > $value) {
                break;
            }

            $formula = $candidate;
            $threshold = $minimum;
        }

        if ($formula === null || $threshold === null) {
            return null;
        }

        return [
            'formula' => $formula,
            'threshold' => $threshold,
        ];
    }

    /** @param array<int,string> $rules @return array<int,string> */
    private function normaliseRules(array $rules): array
    {
        $normalised = [];

        foreach ($rules as $minimum => $formula) {
            $minimum = max(1, (int) $minimum);
            $formula = trim((string) $formula);

            if (
                $formula === ''
                || ! preg_match(
                    '/^\d+d(?:4|6|8|10|12|20|100)$/i',
                    $formula
                )
            ) {
                continue;
            }

            $normalised[$minimum] = strtolower($formula);
        }

        ksort($normalised);

        return $normalised;
    }
}
