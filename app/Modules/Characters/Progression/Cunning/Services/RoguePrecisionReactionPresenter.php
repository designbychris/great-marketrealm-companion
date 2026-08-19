<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Active-play contracts for Rogue precision and reaction features.
 *
 * The Companion exposes player-controlled declarations without pretending to
 * know whether battlefield qualification has been satisfied.
 */
final class RoguePrecisionReactionPresenter
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
            ];
        }

        $level = $character
            ->level()
            ->value();

        $sneakDice = (int) ceil(
            max(1, $level) / 2
        );

        return [
            'supported' => true,
            'sneak_attack' => [
                'unlocked' => true,
                'dice' => $sneakDice . 'd6',
                'frequency' => 'Once per turn',
                'damage_roll' => [
                    'kind' => 'damage',
                    'formula' => $sneakDice . 'd6',
                    'modifier' => 0,
                    'label' => 'Sneak Attack — Precision Damage',
                    'source' => 'Sneak Attack',
                    'result_suffix' => 'Sneak Attack damage',
                ],
                'qualification' => [
                    'The attack must qualify for Sneak Attack under the table’s current rules.',
                    'The Companion does not decide whether advantage, positioning, weapon choice, or another battlefield condition qualifies the attack.',
                    'Mark Sneak Attack used only after applying it to a qualifying hit.',
                ],
            ],
            'uncanny_dodge' => [
                'unlocked' => $level >= 5,
                'level' => 5,
                'frequency' => 'Reaction',
                'summary' =>
                    'When a qualifying visible attacker hits, declare Uncanny Dodge to reduce that attack’s damage.',
                'guidance' =>
                    'The Companion records the reaction declaration but leaves attacker visibility, qualification, and final damage resolution to the table.',
            ],
            'evasion' => [
                'unlocked' => $level >= 7,
                'level' => 7,
                'summary' =>
                    'Evasion is passive resolution guidance rather than a spendable reaction.',
                'guidance' =>
                    'Resolve qualifying Dexterity-save area effects using the Rogue’s certified Evasion feature; no use counter is required.',
            ],
        ];
    }
}
