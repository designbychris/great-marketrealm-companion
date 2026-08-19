<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Player-facing Warlock Eldritch Arts.
 *
 * The repository currently provides Bureaucratic Hex as the Marketrealm
 * equivalent of the Warlock's signature force cantrip. Its level scaling is
 * represented here as independent beams rather than one combined damage roll.
 */
final class WarlockEldritchArtsPresenter
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
            !== 'warlock'
        ) {
            return [
                'supported' => false,
                'beams' => [],
            ];
        }

        $level = $character
            ->level()
            ->value();

        $beamCount = match (true) {
            $level >= 17 => 4,
            $level >= 11 => 3,
            $level >= 5 => 2,
            default => 1,
        };

        $attackBonus =
            $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->charisma()
                ->modifier();

        $beams = [];

        for (
            $beam = 1;
            $beam <= $beamCount;
            $beam++
        ) {
            $beams[] = [
                'number' => $beam,
                'label' =>
                    'Bureaucratic Hex · Beam '
                    . $beam,
                'attack_bonus' =>
                    $attackBonus,
                'damage_formula' =>
                    '1d10',
                'damage_type' =>
                    'force',
                'range' =>
                    '120 ft',
                'target_mode' =>
                    'creature',
            ];
        }

        return [
            'supported' => true,
            'label' =>
                'Bureaucratic Hex',
            'summary' =>
                'Fire each stamped eldritch sigil as its own spell attack. Every beam may be aimed independently.',
            'beam_count' =>
                $beamCount,
            'beams' =>
                $beams,
            'attack_bonus' =>
                $attackBonus,
            'damage_formula' =>
                '1d10',
            'damage_type' =>
                'force',
            'range' =>
                '120 ft',
            'at_will' => true,
            'pact_slot_required' => false,
        ];
    }
}
