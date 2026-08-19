<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Derives usable Barbarian action contracts for the Character Ledger.
 *
 * The presenter does not persist combat state. Rage activation remains owned
 * by III.12.3C's Rage Reserve service. Dice-backed actions reuse Guild
 * Diceworks; non-roll abilities stay explanatory rather than inventing dice.
 */
final class BarbarianPrimalActionPresenter
{
    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        ?ActiveClassConditionState $conditions = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'barbarian'
        ) {
            return [
                'supported' => false,
                'actions' => [],
            ];
        }

        $conditions ??=
            ActiveClassConditionState::fresh();

        $level = $character
            ->level()
            ->value();

        $rageActive =
            $conditions->active('rage');

        $dexteritySave = $character
            ->savingThrows()
            ->dexterity();

        $constitutionSave = $character
            ->savingThrows()
            ->constitution();

        $strength = $character
            ->abilityScores()
            ->strength();

        return [
            'supported' => true,
            'rage_active' => $rageActive,
            'actions' => [
                [
                    'key' => 'rage-damage',
                    'label' => 'Rage Damage',
                    'level' => 1,
                    'unlocked' => true,
                    'available' => $rageActive,
                    'kind' => 'state',
                    'badge' => $rageActive
                        ? '+' . $this->rageDamageBonus($level)
                        : 'Dormant',
                    'detail' =>
                        $rageActive
                            ? 'Rage is active. Apply the certified Rage damage bonus to qualifying melee weapon damage.'
                            : 'Enter Rage before applying the Rage damage bonus.',
                    'roll' => null,
                ],
                [
                    'key' => 'reckless-attack',
                    'label' => 'Reckless Attack',
                    'level' => 2,
                    'unlocked' => $level >= 2,
                    'available' => $level >= 2,
                    'kind' => 'guidance',
                    'badge' => 'Advantage',
                    'detail' =>
                        'When you make your first Strength-based melee weapon attack on your turn, you can choose to attack recklessly. Use the weapon’s normal attack roll in Diceworks and select Advantage.',
                    'roll' => null,
                ],
                [
                    'key' => 'danger-sense',
                    'label' => 'Danger Sense',
                    'level' => 2,
                    'unlocked' => $level >= 2,
                    'available' => $level >= 2,
                    'kind' => 'saving-throw',
                    'badge' => 'DEX Save',
                    'detail' =>
                        'When Danger Sense applies, roll the Dexterity saving throw with Advantage.',
                    'roll' => [
                        'kind' => 'saving-throw',
                        'source' => 'Danger Sense',
                        'ability' => 'dexterity',
                        'proficiency' =>
                            $dexteritySave->isProficient()
                                ? 'proficient'
                                : 'none',
                        'label' =>
                            'Danger Sense — Dexterity Saving Throw',
                        'modifier' =>
                            $dexteritySave->modifier(),
                        'result_suffix' =>
                            'Dexterity saving throw',
                        'default_mode' => 'advantage',
                    ],
                ],
                [
                    'key' => 'brutal-critical',
                    'label' => 'Brutal Critical',
                    'level' => 9,
                    'unlocked' => $level >= 9,
                    'available' => $level >= 9,
                    'kind' => 'guidance',
                    'badge' =>
                        $level >= 17
                            ? '+3 dice'
                            : (
                                $level >= 13
                                    ? '+2 dice'
                                    : '+1 die'
                            ),
                    'detail' =>
                        'On a melee weapon critical hit, add the certified number of extra weapon damage dice after resolving the normal critical damage.',
                    'roll' => null,
                ],
                [
                    'key' => 'relentless-rage',
                    'label' => 'Relentless Rage',
                    'level' => 11,
                    'unlocked' => $level >= 11,
                    'available' =>
                        $level >= 11
                        && $rageActive,
                    'kind' => 'saving-throw',
                    'badge' => 'CON Save',
                    'detail' =>
                        'While Raging, use this Constitution save when Relentless Rage is triggered. Its first DC is 10; repeated successful uses become harder until a rest.',
                    'roll' => [
                        'kind' => 'saving-throw',
                        'source' => 'Relentless Rage',
                        'ability' => 'constitution',
                        'proficiency' =>
                            $constitutionSave->isProficient()
                                ? 'proficient'
                                : 'none',
                        'label' =>
                            'Relentless Rage — Constitution Saving Throw',
                        'modifier' =>
                            $constitutionSave->modifier(),
                        'result_suffix' =>
                            'Relentless Rage save · first DC 10',
                        'default_mode' => 'normal',
                    ],
                ],
                [
                    'key' => 'indomitable-might',
                    'label' => 'Indomitable Might',
                    'level' => 18,
                    'unlocked' => $level >= 18,
                    'available' => $level >= 18,
                    'kind' => 'ability-check',
                    'badge' =>
                        'Minimum ' . $strength->value(),
                    'detail' =>
                        'Roll a Strength check normally. If the resulting total is lower than the raw Strength score, Indomitable Might uses that Strength score instead.',
                    'roll' => [
                        'kind' => 'ability-check',
                        'source' => 'Indomitable Might',
                        'ability' => 'strength',
                        'proficiency' => 'none',
                        'label' =>
                            'Indomitable Might — Strength Check',
                        'modifier' =>
                            $strength->modifier(),
                        'result_suffix' =>
                            'Strength check · minimum '
                            . $strength->value(),
                        'default_mode' => 'normal',
                    ],
                ],
            ],
        ];
    }

    private function rageDamageBonus(
        int $level
    ): int {
        if ($level >= 16) {
            return 4;
        }

        if ($level >= 9) {
            return 3;
        }

        return 2;
    }
}
