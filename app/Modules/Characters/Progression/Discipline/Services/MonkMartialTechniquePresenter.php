<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Usable Monk martial-technique contracts for active play.
 */
final class MonkMartialTechniquePresenter
{
    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        ?ActiveClassResourceState $resources = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'monk'
        ) {
            return [
                'supported' => false,
                'techniques' => [],
            ];
        }

        $resources ??=
            ActiveClassResourceState::fresh();

        $level = $character
            ->level()
            ->value();

        $reserve =
            new MonkDisciplineReserveService();

        $remaining = $level >= 2
            ? $reserve->remaining(
                $character,
                $resources
            )
            : 0;

        $dexterity = $character
            ->abilityScores()
            ->dexterity();

        return [
            'supported' => true,
            'remaining_discipline' =>
                $remaining,
            'save_dc' =>
                (new MonkDisciplinePolicy())
                    ->saveDc($character),
            'techniques' => [
                $this->spendTechnique(
                    'flurry-of-blows',
                    'Flurry of Blows',
                    2,
                    $level,
                    $remaining,
                    'Bonus action',
                    'After taking the Attack action, spend 1 Discipline to make the additional unarmed strikes granted by Flurry of Blows.',
                    'The Companion records the Discipline spend but leaves the individual attack rolls to the normal attack controls.'
                ),
                $this->spendTechnique(
                    'patient-defense',
                    'Patient Defense',
                    2,
                    $level,
                    $remaining,
                    'Bonus action',
                    'Spend 1 Discipline to take the Dodge action as a bonus action.',
                    'No dice roll is invented. The resulting defensive effects are resolved at the table.'
                ),
                $this->spendTechnique(
                    'step-of-the-wind',
                    'Step of the Wind',
                    2,
                    $level,
                    $remaining,
                    'Bonus action',
                    'Spend 1 Discipline to take Dash or Disengage as a bonus action and use the technique’s certified movement benefit.',
                    'Choose the movement option appropriate to the scene; no roll is required.'
                ),
                [
                    'key' => 'deflect-missiles',
                    'label' => 'Deflect Missiles',
                    'level' => 3,
                    'unlocked' => $level >= 3,
                    'available' => $level >= 3,
                    'kind' => 'reaction-roll',
                    'cost' => 0,
                    'badge' => 'Reaction',
                    'summary' =>
                        'Use your reaction when the certified trigger occurs and roll the reduction.',
                    'detail' =>
                        'Reduction is 1d10 + Dexterity modifier + Monk level. If the reduction brings the qualifying missile damage to 0, the table can determine whether returning it is possible.',
                    'roll' => [
                        'kind' => 'damage',
                        'source' =>
                            'Deflect Missiles',
                        'label' =>
                            'Deflect Missiles — Damage Reduction',
                        'formula' => '1d10',
                        'modifier' =>
                            $dexterity->modifier()
                            + $level,
                        'result_suffix' =>
                            'damage reduction',
                    ],
                    'follow_up' => [
                        'key' =>
                            'return-deflected-missile',
                        'label' =>
                            'Return Deflected Missile',
                        'cost' => 1,
                        'available' =>
                            $level >= 3
                            && $remaining >= 1,
                        'detail' =>
                            'Spend only after the table confirms the missile was reduced to 0 and can be returned.',
                    ],
                ],
                $this->spendTechnique(
                    'stunning-strike',
                    'Stunning Strike',
                    5,
                    $level,
                    $remaining,
                    'On hit',
                    'After a qualifying melee weapon hit, spend 1 Discipline to attempt Stunning Strike.',
                    'The target makes the feature’s required saving throw against the Monk’s Discipline Save DC; the Companion does not roll for the target.',
                    'DC '
                        . (new MonkDisciplinePolicy())
                            ->saveDc($character)
                ),
                [
                    'key' => 'slow-fall',
                    'label' => 'Slow Fall',
                    'level' => 4,
                    'unlocked' => $level >= 4,
                    'available' => $level >= 4,
                    'kind' => 'guidance',
                    'cost' => 0,
                    'badge' =>
                        'Reduce '
                        . (5 * $level),
                    'summary' =>
                        'Use the certified reaction when falling.',
                    'detail' =>
                        'Reduce falling damage by up to five times the current Monk level. No Discipline spend or dice roll is required.',
                    'roll' => null,
                ],
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function spendTechnique(
        string $key,
        string $label,
        int $requiredLevel,
        int $level,
        int $remaining,
        string $timing,
        string $summary,
        string $detail,
        string $badge = '1 Discipline'
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'level' => $requiredLevel,
            'unlocked' =>
                $level >= $requiredLevel,
            'available' =>
                $level >= $requiredLevel
                && $remaining >= 1,
            'kind' => 'discipline-spend',
            'cost' => 1,
            'badge' => $badge,
            'timing' => $timing,
            'summary' => $summary,
            'detail' => $detail,
            'roll' => null,
        ];
    }
}
