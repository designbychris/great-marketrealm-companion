<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronPolicy;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Persistent Warlock Pact Magic reserve.
 *
 * Pact slots intentionally use their own resource key because their entire
 * reserve is one current slot level and refreshes on a short or long rest.
 */
final class WarlockPactReserveService
{
    public const RESOURCE = 'pact-magic-slot';

    public function __construct(
        private ?WarlockPatronPolicy $policy = null
    ) {
        $this->policy ??=
            new WarlockPatronPolicy();
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertWarlock($character);

        return $state->spend(
            self::RESOURCE,
            $this->maximum($character)
        );
    }

    public function shortRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertWarlock($character);

        return $state->restore([
            self::RESOURCE,
        ]);
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        return $this->shortRest(
            $character,
            $state
        );
    }

    public function maximum(
        Character $character
    ): int {
        $this->assertWarlock($character);

        return $this->policy
            ->pactSlots($character);
    }

    public function slotLevel(
        Character $character
    ): int {
        $this->assertWarlock($character);

        return $this->policy
            ->pactSlotLevel($character);
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

    /**
     * @return array<int,array{level:int,total:int,remaining:int,expended:int,pact:bool}>
     */
    public function presentSlots(
        Character $character,
        ActiveClassResourceState $state
    ): array {
        $this->assertWarlock($character);

        return [[
            'level' =>
                $this->slotLevel($character),
            'total' =>
                $this->maximum($character),
            'remaining' =>
                $this->remaining(
                    $character,
                    $state
                ),
            'expended' =>
                $this->expended($state),
            'pact' => true,
        ]];
    }

    private function assertWarlock(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'warlock'
        ) {
            throw new InvalidArgumentException(
                'Pact Magic reserves are certified for Warlocks only.'
            );
        }
    }
}
