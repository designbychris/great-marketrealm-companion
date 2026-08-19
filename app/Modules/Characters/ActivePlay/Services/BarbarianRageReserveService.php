<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Barbarian Rage capacity, expenditure and active-state policy.
 */
final class BarbarianRageReserveService
{
    private const RESOURCE = 'rage';
    private const CONDITION = 'rage';

    /**
     * @return array{
     *     resources:ActiveClassResourceState,
     *     conditions:ActiveClassConditionState
     * }
     */
    public function enter(
        Character $character,
        ActiveClassResourceState $resources,
        ActiveClassConditionState $conditions
    ): array {
        $this->assertBarbarian(
            $character
        );

        if (
            $conditions->active(
                self::CONDITION
            )
        ) {
            throw new InvalidArgumentException(
                'This Barbarian is already Raging.'
            );
        }

        if (! $this->unlimited($character)) {
            $resources = $resources->spend(
                self::RESOURCE,
                $this->maximum($character)
            );
        }

        return [
            'resources' => $resources,
            'conditions' =>
                $conditions->activate(
                    self::CONDITION
                ),
        ];
    }

    public function end(
        Character $character,
        ActiveClassConditionState $conditions
    ): ActiveClassConditionState {
        $this->assertBarbarian(
            $character
        );

        return $conditions->deactivate(
            self::CONDITION
        );
    }

    /**
     * @return array{
     *     resources:ActiveClassResourceState,
     *     conditions:ActiveClassConditionState
     * }
     */
    public function longRest(
        Character $character,
        ActiveClassResourceState $resources,
        ActiveClassConditionState $conditions
    ): array {
        $this->assertBarbarian(
            $character
        );

        return [
            'resources' =>
                $resources->restore([
                    self::RESOURCE,
                ]),
            'conditions' =>
                $conditions->deactivate(
                    self::CONDITION
                ),
        ];
    }

    public function maximum(
        Character $character
    ): int {
        $this->assertBarbarian(
            $character
        );

        $level = $character
            ->level()
            ->value();

        if ($level >= 20) {
            return 0;
        }

        if ($level >= 17) {
            return 6;
        }

        if ($level >= 12) {
            return 5;
        }

        if ($level >= 6) {
            return 4;
        }

        if ($level >= 3) {
            return 3;
        }

        return 2;
    }

    public function damageBonus(
        Character $character
    ): int {
        $this->assertBarbarian(
            $character
        );

        $level = $character
            ->level()
            ->value();

        if ($level >= 16) {
            return 4;
        }

        if ($level >= 9) {
            return 3;
        }

        return 2;
    }

    public function unlimited(
        Character $character
    ): bool {
        $this->assertBarbarian(
            $character
        );

        return $character
            ->level()
            ->value()
            >= 20;
    }

    private function assertBarbarian(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'barbarian'
        ) {
            throw new InvalidArgumentException(
                'Rage Reserves are certified for Barbarians only.'
            );
        }
    }
}
