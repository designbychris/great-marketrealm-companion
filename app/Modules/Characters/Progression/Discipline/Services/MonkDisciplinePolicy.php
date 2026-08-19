<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only Monk Discipline progression policy.
 */
final class MonkDisciplinePolicy
{
    public function maximum(
        Character $character
    ): int {
        $this->assertMonk($character);

        $level = $character
            ->level()
            ->value();

        return $level >= 2
            ? $level
            : 0;
    }

    public function saveDc(
        Character $character
    ): int {
        $this->assertMonk($character);

        return 8
            + $character
                ->proficiencyBonus()
                ->value()
            + $character
                ->abilityScores()
                ->wisdom()
                ->modifier();
    }

    public function movementBonusFeet(
        Character $character
    ): int {
        $this->assertMonk($character);

        $level = $character
            ->level()
            ->value();

        if ($level >= 18) {
            return 30;
        }

        if ($level >= 14) {
            return 25;
        }

        if ($level >= 10) {
            return 20;
        }

        if ($level >= 6) {
            return 15;
        }

        return $level >= 2
            ? 10
            : 0;
    }

    private function assertMonk(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            === 'monk'
        ) {
            return;
        }

        throw new InvalidArgumentException(
            'Monk Discipline policy requires a Monk Character.'
        );
    }
}
