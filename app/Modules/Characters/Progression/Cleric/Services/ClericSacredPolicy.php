<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only Cleric sacred calculations for III.12.11A.
 */
final class ClericSacredPolicy
{
    public function spellSaveDc(
        Character $character
    ): int {
        $this->guard($character);

        return 8
            + $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->wisdom()
                ->modifier();
    }

    public function spellAttackBonus(
        Character $character
    ): int {
        $this->guard($character);

        return $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->wisdom()
                ->modifier();
    }

    public function preparedSpellMaximum(
        Character $character
    ): int {
        $this->guard($character);

        return max(
            1,
            $character
                ->level()
                ->value()
            + $character
                ->abilityScores()
                ->wisdom()
                ->modifier()
        );
    }

    public function cantripsKnown(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 10 => 5,
            $level >= 4 => 4,
            default => 3,
        };
    }

    public function maximumSpellLevel(
        Character $character
    ): int {
        $this->guard($character);

        return min(
            9,
            intdiv(
                $character
                    ->level()
                    ->value()
                + 1,
                2
            )
        );
    }

    public function channelDivinityMaximum(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 18 => 3,
            $level >= 6 => 2,
            $level >= 2 => 1,
            default => 0,
        };
    }

    public function destroyUndeadThreshold(
        Character $character
    ): ?string {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 17 => 'CR 4',
            $level >= 14 => 'CR 3',
            $level >= 11 => 'CR 2',
            $level >= 8 => 'CR 1',
            $level >= 5 => 'CR 1/2',
            default => null,
        };
    }

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'cleric'
        ) {
            throw new InvalidArgumentException(
                'Cleric Sacred policy requires a Cleric Character.'
            );
        }
    }
}
