<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only Druid Grove calculations for III.12.10A.
 */
final class DruidGrovePolicy
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

    public function wildShapeStage(
        Character $character
    ): int {
        $this->guard($character);

        $level = $character
            ->level()
            ->value();

        return match (true) {
            $level >= 8 => 3,
            $level >= 4 => 2,
            $level >= 2 => 1,
            default => 0,
        };
    }

    private function guard(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'druid'
        ) {
            throw new InvalidArgumentException(
                'Druid Grove policy requires a Druid Character.'
            );
        }
    }
}
