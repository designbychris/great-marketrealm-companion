<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only Bard performance calculations for III.12.12A.
 */
final class BardPerformancePolicy
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
                ->charisma()
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
                ->charisma()
                ->modifier();
    }

    public function inspirationMaximum(
        Character $character
    ): int {
        $this->guard($character);

        return max(
            1,
            $character
                ->abilityScores()
                ->charisma()
                ->modifier()
        );
    }

    public function inspirationDie(
        Character $character
    ): string {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 15 => 'd12',
            $level >= 10 => 'd10',
            $level >= 5 => 'd8',
            default => 'd6',
        };
    }

    public function inspirationRefresh(
        Character $character
    ): string {
        $this->guard($character);

        return $character
                ->level()
                ->value()
            >= 5
                ? 'short-or-long-rest'
                : 'long-rest';
    }

    public function songOfRestDie(
        Character $character
    ): ?string {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 17 => 'd12',
            $level >= 13 => 'd10',
            $level >= 9 => 'd8',
            $level >= 2 => 'd6',
            default => null,
        };
    }

    public function spellsKnown(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return [
            1 => 4,
            2 => 5,
            3 => 6,
            4 => 7,
            5 => 8,
            6 => 9,
            7 => 10,
            8 => 11,
            9 => 12,
            10 => 14,
            11 => 15,
            12 => 15,
            13 => 16,
            14 => 18,
            15 => 19,
            16 => 19,
            17 => 20,
            18 => 22,
            19 => 22,
            20 => 22,
        ][$level];
    }

    public function cantripsKnown(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 10 => 4,
            $level >= 4 => 3,
            default => 2,
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

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'bard'
        ) {
            throw new InvalidArgumentException(
                'Bard Performance policy requires a Bard Character.'
            );
        }
    }
}
