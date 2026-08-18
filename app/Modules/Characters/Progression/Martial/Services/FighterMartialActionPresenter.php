<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Derives usable Fighter action contracts for the existing Guild Diceworks.
 *
 * Resource expenditure remains owned by Battle Reserves. This presenter only
 * describes the action or roll that accompanies a certified resource.
 */
final class FighterMartialActionPresenter
{
    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'fighter'
        ) {
            return [
                'supported' => false,
                'resources' => [],
            ];
        }

        $level = $character->level()->value();

        return [
            'supported' => true,
            'resources' => [
                'second-wind' => [
                    'button_label' => 'Mark Second Wind Spent',
                    'roll' => [
                        'kind' => 'healing',
                        'formula' => '1d10',
                        'modifier' => $level,
                        'label' =>
                            'Second Wind — Healing',
                        'result_suffix' => 'HP recovered',
                    ],
                ],
                'action-surge' => [
                    'button_label' => 'Use Action Surge',
                    'roll' => null,
                    'note' =>
                        'Spend one reserve, then take one additional action this turn.',
                ],
                'indomitable' => [
                    'button_label' => 'Mark Indomitable Spent',
                    'roll' => null,
                    'save_rerolls' =>
                        $this->savingThrowRerolls(
                            $character
                        ),
                    'note' =>
                        'Spend one reserve after a failed saving throw, then reroll that save.',
                ],
            ],
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function savingThrowRerolls(
        Character $character
    ): array {
        $labels = [
            'strength' => 'STR',
            'dexterity' => 'DEX',
            'constitution' => 'CON',
            'intelligence' => 'INT',
            'wisdom' => 'WIS',
            'charisma' => 'CHA',
        ];

        $rerolls = [];

        foreach (
            $character
                ->savingThrows()
                ->all()
            as $ability => $savingThrow
        ) {
            $rerolls[] = [
                'ability' => $ability,
                'label' =>
                    $labels[$ability]
                    ?? strtoupper($ability),
                'modifier' =>
                    $savingThrow->modifier(),
                'proficient' =>
                    $savingThrow->isProficient(),
            ];
        }

        return $rerolls;
    }
}
