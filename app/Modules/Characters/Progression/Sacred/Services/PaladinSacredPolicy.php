<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only permanent Paladin sacred progression policy.
 */
final class PaladinSacredPolicy
{
    public function layOnHandsMaximum(
        Character $character
    ): int {
        $this->assertPaladin($character);

        return 5
            * $character
                ->level()
                ->value();
    }

    public function divineSenseMaximum(
        Character $character
    ): int {
        $this->assertPaladin($character);

        return max(
            1,
            1
            + $character
                ->abilityScores()
                ->charisma()
                ->modifier()
        );
    }

    public function sacredSaveDc(
        Character $character
    ): int {
        $this->assertPaladin($character);

        return 8
            + $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->charisma()
                ->modifier();
    }

    public function cleansingTouchMaximum(
        Character $character
    ): int {
        $this->assertPaladin($character);

        if (
            $character
                ->level()
                ->value()
            < 14
        ) {
            return 0;
        }

        return max(
            1,
            $character
                ->abilityScores()
                ->charisma()
                ->modifier()
        );
    }

    public function auraRangeFeet(
        Character $character
    ): int {
        $this->assertPaladin($character);

        $level = $character
            ->level()
            ->value();

        if ($level >= 18) {
            return 30;
        }

        return $level >= 6
            ? 10
            : 0;
    }

    private function assertPaladin(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            === 'paladin'
        ) {
            return;
        }

        throw new InvalidArgumentException(
            'Paladin Sacred policy requires a Paladin Character.'
        );
    }
}
