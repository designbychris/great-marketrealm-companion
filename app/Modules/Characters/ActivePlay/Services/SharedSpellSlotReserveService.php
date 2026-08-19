<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Shared spell-slot expenditure over ActiveClassResourceState.
 *
 * This is intentionally class-agnostic so Paladin Divine Smite, Wizard spells,
 * and later spellcasting Callings can all consume one certified slot ledger.
 */
final class SharedSpellSlotReserveService
{
    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        int $slotLevel
    ): ActiveClassResourceState {
        $maximum = $this->maximum(
            $character,
            $slotLevel
        );

        return $state->spend(
            $this->resource($slotLevel),
            $maximum
        );
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $resources = array_map(
            fn (array $slot): string =>
                $this->resource(
                    (int) $slot['level']
                ),
            $this->slots($character)
        );

        return $state->restore($resources);
    }

    public function maximum(
        Character $character,
        int $slotLevel
    ): int {
        foreach ($this->slots($character) as $slot) {
            if (
                (int) $slot['level']
                === $slotLevel
            ) {
                return (int) $slot['total'];
            }
        }

        throw new InvalidArgumentException(
            'That spell-slot level is not available to this adventurer.'
        );
    }

    public function remaining(
        Character $character,
        ActiveClassResourceState $state,
        int $slotLevel
    ): int {
        return $state->remaining(
            $this->resource($slotLevel),
            $this->maximum(
                $character,
                $slotLevel
            )
        );
    }

    /**
     * @return array<int,array{level:int,total:int,remaining:int,expended:int}>
     */
    public function present(
        Character $character,
        ActiveClassResourceState $state
    ): array {
        return array_map(
            function (array $slot) use (
                $character,
                $state
            ): array {
                $level = (int) $slot['level'];
                $total = (int) $slot['total'];

                return [
                    'level' => $level,
                    'total' => $total,
                    'remaining' =>
                        $state->remaining(
                            $this->resource($level),
                            $total
                        ),
                    'expended' =>
                        $state->expended(
                            $this->resource($level)
                        ),
                ];
            },
            $this->slots($character)
        );
    }

    private function resource(
        int $slotLevel
    ): string {
        if (
            $slotLevel < 1
            || $slotLevel > 9
        ) {
            throw new InvalidArgumentException(
                'Spell-slot level must be between 1 and 9.'
            );
        }

        return 'spell-slot-'
            . $slotLevel;
    }

    /**
     * @return array<int,array{level:int,total:int}>
     */
    private function slots(
        Character $character
    ): array {
        return (
            new ArcanePantryPresenter(
                new ArcaneAbilityCatalogue()
            )
        )->present(
            $character
        )['slots'] ?? [];
    }
}
