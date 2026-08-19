<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredPolicy;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Paladin-owned persistent Sacred Reserve policy.
 *
 * Spell slots deliberately remain outside this service. They belong to the
 * shared spellcasting architecture and will later power Divine Smite.
 */
final class PaladinSacredReserveService
{
    public const LAY_ON_HANDS = 'lay-on-hands';
    public const DIVINE_SENSE = 'divine-sense';
    public const CLEANSING_TOUCH = 'cleansing-touch';

    public function __construct(
        private ?PaladinSacredPolicy $policy = null
    ) {
        $this->policy ??=
            new PaladinSacredPolicy();
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        string $resource,
        int $amount = 1
    ): ActiveClassResourceState {
        $this->assertPaladin($character);

        $resource = sanitize_key($resource);

        if ($amount < 1) {
            throw new InvalidArgumentException(
                'A Sacred Reserve spend must use at least one point.'
            );
        }

        $maximum = $this->maximum(
            $character,
            $resource
        );

        if (
            $state->remaining(
                $resource,
                $maximum
            ) < $amount
        ) {
            throw new InvalidArgumentException(
                'The Paladin does not have enough of that Sacred Reserve remaining.'
            );
        }

        $next = $state;

        for ($spent = 0; $spent < $amount; $spent++) {
            $next = $next->spend(
                $resource,
                $maximum
            );
        }

        return $next;
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertPaladin($character);

        return $state->restore([
            self::LAY_ON_HANDS,
            self::DIVINE_SENSE,
            self::CLEANSING_TOUCH,
        ]);
    }

    public function maximum(
        Character $character,
        string $resource
    ): int {
        $this->assertPaladin($character);

        return match (
            sanitize_key($resource)
        ) {
            self::LAY_ON_HANDS =>
                $this->policy
                    ->layOnHandsMaximum(
                        $character
                    ),
            self::DIVINE_SENSE =>
                $this->policy
                    ->divineSenseMaximum(
                        $character
                    ),
            self::CLEANSING_TOUCH =>
                $this->policy
                    ->cleansingTouchMaximum(
                        $character
                    ),
            default => throw new InvalidArgumentException(
                'Unknown Paladin Sacred Reserve.'
            ),
        };
    }

    public function remaining(
        Character $character,
        ActiveClassResourceState $state,
        string $resource
    ): int {
        return $state->remaining(
            $resource,
            $this->maximum(
                $character,
                $resource
            )
        );
    }

    public function expended(
        ActiveClassResourceState $state,
        string $resource
    ): int {
        return $state->expended(
            sanitize_key($resource)
        );
    }

    private function assertPaladin(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'paladin'
        ) {
            throw new InvalidArgumentException(
                'Sacred Reserves are certified for Paladins only.'
            );
        }
    }
}
