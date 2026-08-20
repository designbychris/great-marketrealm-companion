<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Persistent expenditure policy for Cleric Channel Divinity and finite
 * Divine Domain resources.
 *
 * Domain Channel Divinity options reuse CHANNEL_DIVINITY rather than
 * creating one counter per Domain action.
 */
final class ClericSacredReserveService
{
    public const CHANNEL_DIVINITY =
        'cleric-channel-divinity';

    public const SUGARCLOUD_ASCENSION =
        'cleric-sugarcloud-ascension';

    public const HAPPY_HEAL_HOUR =
        'cleric-happy-heal-hour';

    public const STINKY_SALVATION =
        'cleric-stinky-salvation';

    public const HOLY_BUTTERSTORM =
        'cleric-holy-butterstorm';

    public const ZEST =
        'cleric-seasoning-zest';

    public const PERFECT_BALANCE =
        'cleric-perfect-balance';

    public const SACRED_VINTAGE =
        'cleric-sacred-vintage';

    public const FERMENT_TOUCH =
        'cleric-ferment-touch';

    public const MOTHER_CULTURE =
        'cleric-mother-culture';

    /**
     * @return array<int,array<string,mixed>>
     */
    public function reserves(
        Character $character,
        ActiveClassResourceState $state
    ): array {
        $this->assertCleric($character);

        $reserves = [];

        foreach (
            $this->definitions($character)
            as $definition
        ) {
            $resource = (string) (
                $definition['resource']
                ?? ''
            );

            $maximum = (int) (
                $definition['maximum']
                ?? 0
            );

            $definition['expended'] =
                $state->expended($resource);

            $definition['remaining'] =
                $state->remaining(
                    $resource,
                    $maximum
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
        $this->assertCleric($character);

        $definition = $this->definition(
            $character,
            $resource
        );

        $maximum = (int) (
            $definition['maximum']
            ?? 0
        );

        if ($maximum < 1) {
            throw new InvalidArgumentException(
                'That Cleric Sacred Reserve has no certified uses available.'
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
        $this->assertCleric($character);

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
        $this->assertCleric($character);

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

        $domain = $character
            ->callingPath()
            ->value();

        $wisdom = $character
            ->abilityScores()
            ->wisdom()
            ->modifier();

        $definitions = [];

        if ($level >= 2) {
            $definitions[] = [
                'resource' =>
                    self::CHANNEL_DIVINITY,
                'label' =>
                    'Channel Divinity',
                'maximum' =>
                    match (true) {
                        $level >= 18 => 3,
                        $level >= 6 => 2,
                        default => 1,
                    },
                'refresh' =>
                    'short-or-long-rest',
                'level' => 2,
                'basis' =>
                    match (true) {
                        $level >= 18 =>
                            '3 uses per short or long rest',
                        $level >= 6 =>
                            '2 uses per short or long rest',
                        default =>
                            '1 use per short or long rest',
                    },
            ];
        }

        return array_merge(
            $definitions,
            $this->domainDefinitions(
                $domain,
                $level,
                $wisdom
            )
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function domainDefinitions(
        string $domain,
        int $level,
        int $wisdom
    ): array {
        return match ($domain) {
            'domain-of-sweetness' =>
                $level >= 17
                    ? [[
                        'resource' =>
                            self::SUGARCLOUD_ASCENSION,
                        'label' =>
                            'Ascension of the Sugarcloud — Free Use',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 17,
                        'basis' =>
                            'Once per long rest, or expend a 5th-level spell slot',
                    ]]
                    : [],

            'domain-of-the-golden-arches' =>
                $level >= 17
                    ? [[
                        'resource' =>
                            self::HAPPY_HEAL_HOUR,
                        'label' =>
                            'Happy Heal Hour',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 17,
                        'basis' =>
                            'Once per long rest',
                    ]]
                    : [],

            'domain-of-dairy' =>
                array_values(
                    array_filter([
                        $level >= 6
                            ? $this->fixed(
                                self::STINKY_SALVATION,
                                'Stinky Salvation',
                                6,
                                'long-rest',
                                'Once per long rest'
                            )
                            : null,
                        $level >= 17
                            ? $this->fixed(
                                self::HOLY_BUTTERSTORM,
                                'Holy Butterstorm',
                                17,
                                'long-rest',
                                'Once per long rest'
                            )
                            : null,
                    ])
                ),

            'domain-of-seasoning' =>
                array_values(
                    array_filter([
                        $level >= 1
                            ? $this->fixed(
                                self::ZEST,
                                'Zest',
                                1,
                                'long-rest',
                                'Once per long rest'
                            )
                            : null,
                        $level >= 17
                            ? $this->fixed(
                                self::PERFECT_BALANCE,
                                'Perfect Balance',
                                17,
                                'long-rest',
                                'Once per long rest'
                            )
                            : null,
                    ])
                ),

            'domain-of-cultivation' =>
                $level >= 17
                    ? [[
                        'resource' =>
                            self::SACRED_VINTAGE,
                        'label' =>
                            'Sacred Vintage',
                        'maximum' => 1,
                        'refresh' =>
                            'long-rest',
                        'level' => 17,
                        'basis' =>
                            'Once per long rest',
                    ]]
                    : [],

            'domain-of-fermentation' =>
                array_values(
                    array_filter([
                        $level >= 1
                            ? [
                                'resource' =>
                                    self::FERMENT_TOUCH,
                                'label' =>
                                    'Ferment Touch',
                                'maximum' =>
                                    max(1, $wisdom),
                                'refresh' =>
                                    'long-rest',
                                'level' => 1,
                                'basis' =>
                                    'Wisdom modifier uses per long rest; ally healing is also once per creature per long rest',
                            ]
                            : null,
                        $level >= 17
                            ? $this->fixed(
                                self::MOTHER_CULTURE,
                                'Mother Culture',
                                17,
                                'long-rest',
                                'Once per long rest'
                            )
                            : null,
                    ])
                ),

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
            'That Sacred Reserve is not certified for this Cleric Domain and level.'
        );
    }

    private function assertCleric(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'cleric'
        ) {
            throw new InvalidArgumentException(
                'Sacred Reserves are certified for Clerics only.'
            );
        }
    }
}
