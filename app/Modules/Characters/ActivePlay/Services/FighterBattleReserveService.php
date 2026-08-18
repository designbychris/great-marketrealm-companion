<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Fighter-specific capability policy over the generic resource state.
 */
final class FighterBattleReserveService
{
    private const SECOND_WIND = 'second-wind';
    private const ACTION_SURGE = 'action-surge';
    private const INDOMITABLE = 'indomitable';

    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        string $resource
    ): ActiveClassResourceState {
        $maximum = $this->maximum(
            $character,
            $resource
        );

        return $state->spend(
            $resource,
            $maximum
        );
    }

    public function shortRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertFighter($character);

        return $state->restore([
            self::SECOND_WIND,
            self::ACTION_SURGE,
        ]);
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertFighter($character);

        return $state->restoreAll();
    }

    public function maximum(
        Character $character,
        string $resource
    ): int {
        $this->assertFighter($character);

        $level = $character->level()->value();

        return match (
            sanitize_key($resource)
        ) {
            self::SECOND_WIND => 1,
            self::ACTION_SURGE =>
                $level >= 17
                    ? 2
                    : ($level >= 2 ? 1 : 0),
            self::INDOMITABLE =>
                $level >= 17
                    ? 3
                    : (
                        $level >= 13
                            ? 2
                            : ($level >= 9 ? 1 : 0)
                    ),
            default => throw new InvalidArgumentException(
                'Unknown Fighter battle reserve.'
            ),
        };
    }

    private function assertFighter(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'fighter'
        ) {
            throw new InvalidArgumentException(
                'Battle Reserves are currently certified for Fighters only.'
            );
        }
    }
}
