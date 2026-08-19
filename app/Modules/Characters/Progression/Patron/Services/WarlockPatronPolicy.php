<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only permanent Warlock progression policy.
 */
final class WarlockPatronPolicy
{
    public function pactSlotLevel(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 9 => 5,
            $level >= 7 => 4,
            $level >= 5 => 3,
            $level >= 3 => 2,
            default => 1,
        };
    }

    public function pactSlots(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 17 => 4,
            $level >= 11 => 3,
            $level >= 2 => 2,
            default => 1,
        };
    }

    public function invocationsKnown(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 18 => 8,
            $level >= 15 => 7,
            $level >= 12 => 6,
            $level >= 9 => 5,
            $level >= 7 => 4,
            $level >= 5 => 3,
            $level >= 2 => 2,
            default => 0,
        };
    }

    /**
     * @return array<int,int>
     */
    public function mysticArcanumLevels(
        Character $character
    ): array {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        $levels = [];

        if ($level >= 11) {
            $levels[] = 6;
        }

        if ($level >= 13) {
            $levels[] = 7;
        }

        if ($level >= 15) {
            $levels[] = 8;
        }

        if ($level >= 17) {
            $levels[] = 9;
        }

        return $levels;
    }

    public function pactSaveDc(
        Character $character
    ): int {
        $this->guard($character);

        return 8
            + $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->charisma()
                ->modifier();
    }

    public function pactAttackBonus(
        Character $character
    ): int {
        $this->guard($character);

        return $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->charisma()
                ->modifier();
    }

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'warlock'
        ) {
            throw new InvalidArgumentException(
                'Warlock Patron policy requires a Warlock Character.'
            );
        }
    }
}
