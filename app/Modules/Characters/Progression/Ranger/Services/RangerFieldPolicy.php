<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only Ranger field progression policy.
 */
final class RangerFieldPolicy
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

    public function extraAttackUnlocked(
        Character $character
    ): bool {
        $this->guard($character);

        return $character
            ->level()
            ->value() >= 5;
    }

    public function favouredMarkStage(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 14 => 3,
            $level >= 6 => 2,
            default => 1,
        };
    }

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'ranger'
        ) {
            throw new InvalidArgumentException(
                'Ranger Field policy requires a Ranger Character.'
            );
        }
    }
}
