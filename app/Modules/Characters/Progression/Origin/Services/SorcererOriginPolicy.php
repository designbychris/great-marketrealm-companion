<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only permanent Sorcerer progression policy.
 */
final class SorcererOriginPolicy
{
    public function sorceryPointMaximum(
        Character $character
    ): int {
        $this->guard($character);

        return $character
            ->level()
            ->value() >= 2
                ? $character
                    ->level()
                    ->value()
                : 0;
    }

    public function metamagicKnown(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 17 => 4,
            $level >= 10 => 3,
            $level >= 3 => 2,
            default => 0,
        };
    }

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

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'sorcerer'
        ) {
            throw new InvalidArgumentException(
                'Sorcerer Origin policy requires a Sorcerer Character.'
            );
        }
    }
}
