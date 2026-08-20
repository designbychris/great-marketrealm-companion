<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Persistent expenditure policy for Ranger Path abilities whose supplied
 * rules define an explicit finite use count.
 */
final class RangerFieldReserveService
{
    public const GRASPING_ROOTS =
        'ranger-grasping-roots';

    public const HEART_OF_THE_ROOTLANDS =
        'ranger-heart-of-the-rootlands';

    public const MIRACLE_HARVEST =
        'ranger-miracle-harvest';

    public const FINAL_SEASONING =
        'ranger-final-seasoning';

    public const ANCIENT_RIND =
        'ranger-ancient-rind';

    public const ANCIENT_SEED =
        'ranger-ancient-seed';

    public const PUT_IT_BACK =
        'ranger-put-it-back';

    /**
     * @return array<int,array<string,mixed>>
     */
    public function reserves(
        Character $character,
        ActiveClassResourceState $state
    ): array {
        $this->assertRanger($character);

        $definitions =
            $this->definitions(
                $character
            );

        $reserves = [];

        foreach ($definitions as $definition) {
            $maximum = (int) (
                $definition['maximum']
                ?? 0
            );

            $resource = (string) (
                $definition['resource']
                ?? ''
            );

            $definition['remaining'] =
                $maximum > 0
                    ? $state->remaining(
                        $resource,
                        $maximum
                    )
                    : 0;

            $definition['expended'] =
                $state->expended(
                    $resource
                );

            $reserves[] = $definition;
        }

        return $reserves;
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        string $resource
    ): ActiveClassResourceState {
        $this->assertRanger($character);

        $definition =
            $this->definition(
                $character,
                $resource
            );

        $maximum = (int) (
            $definition['maximum']
            ?? 0
        );

        if ($maximum < 1) {
            throw new InvalidArgumentException(
                'That Ranger Field Reserve has no certified uses available.'
            );
        }

        return $state->spend(
            (string) $definition['resource'],
            $maximum
        );
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertRanger($character);

        return $state->restore(
            array_column(
                $this->definitions(
                    $character
                ),
                'resource'
            )
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function definitions(
        Character $character
    ): array {
        $path = $character
            ->callingPath()
            ->value();

        $level = $character
            ->level()
            ->value();

        $pb = $character
            ->proficiencyBonus()
            ->value();

        $wisdom = $character
            ->abilityScores()
            ->wisdom()
            ->modifier();

        return match ($path) {
            'deep-root-warden' =>
                array_values(
                    array_filter([
                        $level >= 3
                            ? [
                                'resource' =>
                                    self::GRASPING_ROOTS,
                                'label' =>
                                    'Grasping Roots',
                                'maximum' => $pb,
                                'refresh' =>
                                    'long-rest',
                                'level' => 3,
                                'basis' =>
                                    'Proficiency Bonus uses per long rest',
                            ]
                            : null,
                        $level >= 15
                            ? [
                                'resource' =>
                                    self::HEART_OF_THE_ROOTLANDS,
                                'label' =>
                                    'Heart of the Rootlands',
                                'maximum' => 1,
                                'refresh' =>
                                    'long-rest',
                                'level' => 15,
                                'basis' =>
                                    'Once per long rest',
                            ]
                            : null,
                    ])
                ),

            'conclave-of-the-forager' =>
                $level >= 15
                    ? [[
                        'resource' =>
                            self::MIRACLE_HARVEST,
                        'label' =>
                            'Miracle Harvest',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 15,
                        'basis' =>
                            'Once per long rest',
                    ]]
                    : [],

            'spice-trail-hunter' =>
                $level >= 15
                    ? [[
                        'resource' =>
                            self::FINAL_SEASONING,
                        'label' =>
                            'The Final Seasoning',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 15,
                        'basis' =>
                            'Once per long rest',
                    ]]
                    : [],

            'rindrunner' =>
                $level >= 15
                    ? [[
                        'resource' =>
                            self::ANCIENT_RIND,
                        'label' =>
                            'Ancient Rind',
                        'maximum' =>
                            max(0, $wisdom),
                        'refresh' =>
                            'long-rest',
                        'level' => 15,
                        'basis' =>
                            'Wisdom modifier uses per long rest',
                    ]]
                    : [],

            'seedshot-conclave' =>
                $level >= 15
                    ? [[
                        'resource' =>
                            self::ANCIENT_SEED,
                        'label' =>
                            'Ancient Seed',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 15,
                        'basis' =>
                            'Once per long rest',
                    ]]
                    : [],

            'expiry-hunter' =>
                $level >= 11
                    ? [[
                        'resource' =>
                            self::PUT_IT_BACK,
                        'label' =>
                            'Put It Back',
                        'maximum' => $pb,
                        'refresh' =>
                            'long-rest',
                        'level' => 11,
                        'basis' =>
                            'Proficiency Bonus uses per long rest',
                    ]]
                    : [],

            default => [],
        };
    }

    /** @return array<string,mixed> */
    private function definition(
        Character $character,
        string $resource
    ): array {
        $resource = sanitize_key(
            $resource
        );

        foreach (
            $this->definitions(
                $character
            )
            as $definition
        ) {
            if (
                ($definition['resource'] ?? '')
                === $resource
            ) {
                return $definition;
            }
        }

        throw new InvalidArgumentException(
            'That Field Reserve is not certified for this Ranger Path and level.'
        );
    }

    private function assertRanger(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'ranger'
        ) {
            throw new InvalidArgumentException(
                'Field Reserves are certified for Rangers only.'
            );
        }
    }
}
