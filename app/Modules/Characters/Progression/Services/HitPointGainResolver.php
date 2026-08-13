<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use RuntimeException;

defined('ABSPATH') || exit;

final class HitPointGainResolver
{
    /**
     * @param array<string,array<int,string>> $choices
     */
    public function resolve(
        Character $character,
        array $choices
    ): int {
        $method = (string) (
            $choices['vitality-hit-points'][0]
            ?? ''
        );

        $constitution = $character
            ->abilityScores()
            ->constitution()
            ->modifier();

        $hitDie = $character
            ->characterClass()
            ->hitDie();

        return match ($method) {
            'average' => max(
                1,
                1 + intdiv($hitDie, 2)
                    + $constitution
            ),
            'roll' => max(
                1,
                random_int(1, $hitDie)
                    + $constitution
            ),
            default => throw new RuntimeException(
                'The Vitality Folio has not recorded a valid HP method.'
            ),
        };
    }
}
