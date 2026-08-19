<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginPolicy;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Persistent Font of Magic reserve and Flexible Casting policy.
 */
final class SorcererSorceryReserveService
{
    public const RESOURCE = 'sorcery-points';

    /** @var array<int,int> */
    private const SLOT_COSTS = [
        1 => 2,
        2 => 3,
        3 => 5,
        4 => 6,
        5 => 7,
    ];

    public function __construct(
        private ?SorcererOriginPolicy $policy = null,
        private ?SharedSpellSlotReserveService $slots = null
    ) {
        $this->policy ??=
            new SorcererOriginPolicy();

        $this->slots ??=
            new SharedSpellSlotReserveService();
    }

    public function maximum(
        Character $character
    ): int {
        $this->assertSorcerer($character);

        return $this->policy
            ->sorceryPointMaximum(
                $character
            );
    }

    public function remaining(
        Character $character,
        ActiveClassResourceState $state
    ): int {
        return $state->remaining(
            self::RESOURCE,
            $this->maximum($character)
        );
    }

    public function expended(
        ActiveClassResourceState $state
    ): int {
        return $state->expended(
            self::RESOURCE
        );
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        int $amount = 1
    ): ActiveClassResourceState {
        $this->assertUnlocked($character);

        if ($amount < 1) {
            throw new InvalidArgumentException(
                'A Sorcery Point spend must use at least one point.'
            );
        }

        $maximum = $this->maximum(
            $character
        );

        if (
            $state->remaining(
                self::RESOURCE,
                $maximum
            ) < $amount
        ) {
            throw new InvalidArgumentException(
                'The Sorcerer does not have enough Sorcery Points remaining.'
            );
        }

        $next = $state;

        for ($spent = 0; $spent < $amount; $spent++) {
            $next = $next->spend(
                self::RESOURCE,
                $maximum
            );
        }

        return $next;
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertSorcerer($character);

        return $state->restore([
            self::RESOURCE,
        ]);
    }

    public function createSpellSlot(
        Character $character,
        ActiveClassResourceState $state,
        int $slotLevel
    ): ActiveClassResourceState {
        $this->assertUnlocked($character);

        $cost = self::SLOT_COSTS[
            $slotLevel
        ] ?? null;

        if ($cost === null) {
            throw new InvalidArgumentException(
                'Font of Magic can create spell slots from Level 1 through Level 5.'
            );
        }

        /*
         * A created slot is represented by recovering one expended slot from
         * the shared standard spell-slot ledger. This prevents the persistent
         * reserve from exceeding its certified maximum.
         */
        if (
            $this->slots->remaining(
                $character,
                $state,
                $slotLevel
            ) >= $this->slots->maximum(
                $character,
                $slotLevel
            )
        ) {
            throw new InvalidArgumentException(
                'That spell-slot reserve is already full.'
            );
        }

        $next = $this->spend(
            $character,
            $state,
            $cost
        );

        return $next->recover(
            'spell-slot-' . $slotLevel
        );
    }

    public function convertSpellSlot(
        Character $character,
        ActiveClassResourceState $state,
        int $slotLevel
    ): ActiveClassResourceState {
        $this->assertUnlocked($character);

        if (
            $this->remaining(
                $character,
                $state
            ) >= $this->maximum(
                $character
            )
        ) {
            throw new InvalidArgumentException(
                'The Sorcery Point reserve is already full.'
            );
        }

        $next = $this->slots->spend(
            $character,
            $state,
            $slotLevel
        );

        $recover = min(
            $slotLevel,
            $next->expended(
                self::RESOURCE
            )
        );

        if ($recover < 1) {
            throw new InvalidArgumentException(
                'The Sorcery Point reserve cannot receive that conversion.'
            );
        }

        return $next->recover(
            self::RESOURCE,
            $recover
        );
    }

    public function slotCreationCost(
        int $slotLevel
    ): int {
        $cost = self::SLOT_COSTS[
            $slotLevel
        ] ?? null;

        if ($cost === null) {
            throw new InvalidArgumentException(
                'Font of Magic can create spell slots from Level 1 through Level 5.'
            );
        }

        return $cost;
    }

    /** @return array<int,array{level:int,cost:int}> */
    public function slotCreationCosts(): array
    {
        $costs = [];

        foreach (self::SLOT_COSTS as $level => $cost) {
            $costs[] = [
                'level' => $level,
                'cost' => $cost,
            ];
        }

        return $costs;
    }

    private function assertUnlocked(
        Character $character
    ): void {
        $this->assertSorcerer($character);

        if (
            $character
                ->level()
                ->value()
            < 2
        ) {
            throw new InvalidArgumentException(
                'Font of Magic is certified from Sorcerer Level 2.'
            );
        }
    }

    private function assertSorcerer(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'sorcerer'
        ) {
            throw new InvalidArgumentException(
                'Sorcery Reserves are certified for Sorcerers only.'
            );
        }
    }
}
