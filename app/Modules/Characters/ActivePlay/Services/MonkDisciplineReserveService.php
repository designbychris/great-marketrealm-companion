<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplinePolicy;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Monk Discipline expenditure and rest policy.
 */
final class MonkDisciplineReserveService
{
    public const RESOURCE = 'discipline';

    public function __construct(
        private ?MonkDisciplinePolicy $policy = null
    ) {
        $this->policy ??=
            new MonkDisciplinePolicy();
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertMonk($character);

        return $state->spend(
            self::RESOURCE,
            $this->maximum($character)
        );
    }

    public function spendTechnique(
        Character $character,
        ActiveClassResourceState $state,
        string $technique
    ): ActiveClassResourceState {
        $this->assertMonk($character);

        $technique = sanitize_key(
            $technique
        );

        $requiredLevel = match ($technique) {
            'flurry-of-blows',
            'patient-defense',
            'step-of-the-wind' => 2,
            'return-deflected-missile' => 3,
            'stunning-strike' => 5,
            default => throw new InvalidArgumentException(
                'Unknown Monk Discipline technique.'
            ),
        };

        if (
            $character
                ->level()
                ->value()
            < $requiredLevel
        ) {
            throw new InvalidArgumentException(
                'This Monk has not yet certified that Discipline technique.'
            );
        }

        return $this->spend(
            $character,
            $state
        );
    }

    public function shortRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertMonk($character);

        return $state->restore([
            self::RESOURCE,
        ]);
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertMonk($character);

        return $state->restore([
            self::RESOURCE,
        ]);
    }

    public function maximum(
        Character $character
    ): int {
        $this->assertMonk($character);

        return $this->policy->maximum(
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

    private function assertMonk(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'monk'
        ) {
            throw new InvalidArgumentException(
                'Discipline Reserves are certified for Monks only.'
            );
        }
    }
}
