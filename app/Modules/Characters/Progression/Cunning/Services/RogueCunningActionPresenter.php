<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Active-use contracts for Rogue Cunning Action.
 *
 * Cunning Action is not a finite resource. Dash and Disengage are declarations
 * rather than dice rolls; Hide delegates its Dexterity (Stealth) check to the
 * shared Guild Diceworks using the Character's real skill state.
 */
final class RogueCunningActionPresenter
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
            !== 'rogue'
        ) {
            return [
                'supported' => false,
                'unlocked' => false,
                'actions' => [],
            ];
        }

        $unlocked = $character
            ->level()
            ->value()
            >= 2;

        $stealth = $character
            ->skills()
            ->stealth();

        return [
            'supported' => true,
            'unlocked' => $unlocked,
            'cost' => 'Bonus action',
            'refresh' => 'Every turn',
            'actions' => [
                [
                    'key' => 'dash',
                    'label' => 'Dash',
                    'icon' => '➜',
                    'unlocked' => $unlocked,
                    'kind' => 'declaration',
                    'summary' =>
                        'Take the Dash action as your Cunning Action.',
                    'detail' =>
                        'Increase movement for the turn using Dash. No resource is spent and no roll is required.',
                    'roll' => null,
                ],
                [
                    'key' => 'disengage',
                    'label' => 'Disengage',
                    'icon' => '↝',
                    'unlocked' => $unlocked,
                    'kind' => 'declaration',
                    'summary' =>
                        'Take the Disengage action as your Cunning Action.',
                    'detail' =>
                        'Slip out of immediate danger using Disengage. No resource is spent and no roll is required.',
                    'roll' => null,
                ],
                [
                    'key' => 'hide',
                    'label' => 'Hide',
                    'icon' => '◐',
                    'unlocked' => $unlocked,
                    'kind' => 'ability-check',
                    'summary' =>
                        'Attempt to Hide using the Rogue’s real Dexterity (Stealth) check.',
                    'detail' =>
                        'Whether hiding is possible depends on the scene. When the attempt is allowed, Guild Diceworks uses this Character’s actual Stealth modifier.',
                    'roll' => [
                        'kind' => 'ability-check',
                        'source' => 'Cunning Action — Hide',
                        'ability' => 'dexterity',
                        'proficiency' =>
                            $stealth->hasExpertise()
                                ? 'expertise'
                                : (
                                    $stealth->isProficient()
                                        ? 'proficient'
                                        : 'none'
                                ),
                        'label' =>
                            'Cunning Action — Hide',
                        'modifier' =>
                            $stealth->modifier(),
                        'result_suffix' =>
                            'Dexterity (Stealth) check',
                        'default_mode' => 'normal',
                    ],
                ],
            ],
        ];
    }
}
