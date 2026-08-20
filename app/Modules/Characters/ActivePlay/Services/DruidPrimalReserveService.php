<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Persistent expenditure policy for core Druid Wild Shape and Circle
 * abilities whose supplied rules define explicit finite use counts.
 */
final class DruidPrimalReserveService
{
    public const WILD_SHAPE = 'druid-wild-shape';

    public const CRISP_AURA_EXPANSION =
        'druid-crisp-aura-expansion';
    public const PRESERVATIVE_PURGE =
        'druid-preservative-purge';

    public const SPICE_BASILISK_BREATH =
        'druid-spice-basilisk-breath';
    public const SCORCHING_BLOOM =
        'druid-scorching-bloom';
    public const PUNGENT_FLAME =
        'druid-pungent-flame';

    public const LIVING_EARTHQUAKE =
        'druid-living-earthquake';

    public const COMPOST_SURGE =
        'druid-compost-surge';
    public const MULCHBORN =
        'druid-mulchborn';
    public const BLOOM_OF_DECAY =
        'druid-bloom-of-decay';
    public const BLIGHT =
        'druid-compost-blight';
    public const INSECT_PLAGUE =
        'druid-compost-insect-plague';

    public const ANIMATE_SPOIL =
        'druid-animate-spoil';

    public const FROZEN_CURD =
        'druid-frozen-curd';
    public const GLACIAL_GROWTH =
        'druid-glacial-growth';
    public const TRUE_CHURNFORM =
        'druid-true-churnform';

    /**
     * @return array<int,array<string,mixed>>
     */
    public function reserves(
        Character $character,
        ActiveClassResourceState $state
    ): array {
        $this->assertDruid($character);

        $reserves = [];

        foreach (
            $this->definitions($character)
            as $definition
        ) {
            $resource = (string) (
                $definition['resource']
                ?? ''
            );

            $maximum = $definition['maximum']
                ?? null;

            $definition['expended'] =
                $state->expended($resource);

            $definition['remaining'] =
                is_int($maximum)
                    ? $state->remaining(
                        $resource,
                        $maximum
                    )
                    : null;

            $reserves[] = $definition;
        }

        return $reserves;
    }

    public function spend(
        Character $character,
        ActiveClassResourceState $state,
        string $resource
    ): ActiveClassResourceState {
        $this->assertDruid($character);

        $definition = $this->definition(
            $character,
            $resource
        );

        if (
            ! empty($definition['unlimited'])
        ) {
            return $state;
        }

        $maximum = $definition['maximum']
            ?? null;

        if (
            ! is_int($maximum)
            || $maximum < 1
        ) {
            throw new InvalidArgumentException(
                'That Druid Primal Reserve has no certified uses available.'
            );
        }

        return $state->spend(
            (string) $definition['resource'],
            $maximum
        );
    }

    public function shortRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertDruid($character);

        return $state->restore(
            $this->resourcesForRefresh(
                $character,
                'short-rest'
            )
        );
    }

    public function longRest(
        Character $character,
        ActiveClassResourceState $state
    ): ActiveClassResourceState {
        $this->assertDruid($character);

        return $state->restore(
            array_column(
                $this->definitions($character),
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
        $level = $character
            ->level()
            ->value();

        $path = $character
            ->callingPath()
            ->value();

        $pb = $character
            ->proficiencyBonus()
            ->value();

        $definitions = [];

        if ($level >= 2) {
            $definitions[] = [
                'resource' => self::WILD_SHAPE,
                'label' => 'Wild Shape',
                'maximum' =>
                    $level >= 20
                        ? null
                        : 2,
                'unlimited' =>
                    $level >= 20,
                'refresh' => 'short-or-long-rest',
                'level' => 2,
                'basis' =>
                    $level >= 20
                        ? 'Unlimited at Archdruid'
                        : '2 uses per short or long rest',
            ];
        }

        return array_merge(
            $definitions,
            $this->circleDefinitions(
                $path,
                $level,
                $pb
            )
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function circleDefinitions(
        string $path,
        int $level,
        int $pb
    ): array {
        return match ($path) {
            'circle-of-eating-fresh' =>
                array_values(array_filter([
                    $level >= 2
                        ? $this->fixed(
                            self::CRISP_AURA_EXPANSION,
                            'Crisp Aura Expansion',
                            2,
                            'long-rest',
                            'Once per long rest'
                        )
                        : null,
                    $level >= 14
                        ? $this->fixed(
                            self::PRESERVATIVE_PURGE,
                            'Preservative Purge',
                            14,
                            'long-rest',
                            'Once per long rest'
                        )
                        : null,
                ])),

            'circle-of-the-groveflame' =>
                array_values(array_filter([
                    $level >= 10
                        ? $this->fixed(
                            self::SPICE_BASILISK_BREATH,
                            'Spice Basilisk Breath',
                            10,
                            'short-or-long-rest',
                            'Once per short rest while in Spice Basilisk form'
                        )
                        : null,
                    $level >= 14
                        ? $this->fixed(
                            self::SCORCHING_BLOOM,
                            'Scorching Bloom',
                            14,
                            'long-rest',
                            'Once per long rest'
                        )
                        : null,
                    $level >= 14
                        ? $this->fixed(
                            self::PUNGENT_FLAME,
                            'Pungent Flame',
                            14,
                            'short-or-long-rest',
                            'Once per short or long rest'
                        )
                        : null,
                ])),

            'circle-of-the-deep-soil' =>
                $level >= 14
                    ? [
                        $this->fixed(
                            self::LIVING_EARTHQUAKE,
                            'Living Earthquake',
                            14,
                            'long-rest',
                            'Once per long rest'
                        ),
                    ]
                    : [],

            'circle-of-the-compost' =>
                array_values(array_filter([
                    $level >= 2
                        ? [
                            'resource' =>
                                self::COMPOST_SURGE,
                            'label' =>
                                'Compost Surge',
                            'maximum' => $pb,
                            'unlimited' => false,
                            'refresh' => 'long-rest',
                            'level' => 2,
                            'basis' =>
                                'Proficiency Bonus uses per long rest',
                        ]
                        : null,
                    $level >= 6
                        ? $this->fixed(
                            self::MULCHBORN,
                            'Mulchborn',
                            6,
                            'short-or-long-rest',
                            'Once per short rest'
                        )
                        : null,
                    $level >= 10
                        ? $this->fixed(
                            self::BLOOM_OF_DECAY,
                            'Bloom of Decay',
                            10,
                            'long-rest',
                            'Once per long rest'
                        )
                        : null,
                    $level >= 10
                        ? $this->fixed(
                            self::BLIGHT,
                            'Blight — Circle Grant',
                            10,
                            'long-rest',
                            'Once per long rest without a spell slot'
                        )
                        : null,
                    $level >= 10
                        ? $this->fixed(
                            self::INSECT_PLAGUE,
                            'Insect Plague — Circle Grant',
                            10,
                            'long-rest',
                            'Once per long rest without a spell slot'
                        )
                        : null,
                ])),

            'circle-of-curdle' =>
                $level >= 10
                    ? [
                        $this->fixed(
                            self::ANIMATE_SPOIL,
                            'Animate Spoil',
                            10,
                            'long-rest',
                            'Once per long rest'
                        ),
                    ]
                    : [],

            'circle-of-the-churn' =>
                array_values(array_filter([
                    $level >= 2
                        ? $this->fixed(
                            self::FROZEN_CURD,
                            'Frozen Curd — Free Use',
                            2,
                            'long-rest',
                            'Once per long rest, or by expending Wild Shape'
                        )
                        : null,
                    $level >= 10
                        ? [
                            'resource' =>
                                self::GLACIAL_GROWTH,
                            'label' =>
                                'Glacial Growth',
                            'maximum' => $pb,
                            'unlimited' => false,
                            'refresh' => 'long-rest',
                            'level' => 10,
                            'basis' =>
                                'Proficiency Bonus uses per long rest',
                        ]
                        : null,
                    $level >= 14
                        ? $this->fixed(
                            self::TRUE_CHURNFORM,
                            'True Churnform',
                            14,
                            'long-rest',
                            'Once per long rest'
                        )
                        : null,
                ])),

            default => [],
        };
    }

    /**
     * @return array<string,mixed>
     */
    private function fixed(
        string $resource,
        string $label,
        int $level,
        string $refresh,
        string $basis
    ): array {
        return [
            'resource' => $resource,
            'label' => $label,
            'maximum' => 1,
            'unlimited' => false,
            'refresh' => $refresh,
            'level' => $level,
            'basis' => $basis,
        ];
    }

    /**
     * @return array<int,string>
     */
    private function resourcesForRefresh(
        Character $character,
        string $refresh
    ): array {
        $resources = [];

        foreach (
            $this->definitions($character)
            as $definition
        ) {
            $policy = (string) (
                $definition['refresh']
                ?? ''
            );

            if (
                $refresh === 'short-rest'
                && $policy !== 'short-or-long-rest'
            ) {
                continue;
            }

            $resources[] = (string) (
                $definition['resource']
                ?? ''
            );
        }

        return $resources;
    }

    /**
     * @return array<string,mixed>
     */
    private function definition(
        Character $character,
        string $resource
    ): array {
        $resource = sanitize_key($resource);

        foreach (
            $this->definitions($character)
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
            'That Primal Reserve is not certified for this Druid Circle and level.'
        );
    }

    private function assertDruid(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'druid'
        ) {
            throw new InvalidArgumentException(
                'Primal Reserves are certified for Druids only.'
            );
        }
    }
}
