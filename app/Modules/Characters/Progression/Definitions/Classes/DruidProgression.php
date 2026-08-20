<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Druid Calling.
 *
 * III.12.10 establishes the permanent Druid spine. Circle gifts, Wild Shape
 * expenditure and active nature techniques remain later Druid slices.
 */
final class DruidProgression implements ClassProgressionDefinitionInterface
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public function foundations(
        CharacterClass $class
    ): array {
        $this->guard($class);

        return [
            [
                'key' => 'druidic',
                'label' => 'Druidic',
                'detail' =>
                    'The Druid begins with the secret language and field-lore of the old growing traditions.',
            ],
            [
                'key' => 'spellcasting',
                'label' => 'Spellcasting',
                'detail' =>
                    'The Druid is a Wisdom-based full caster who prepares spells from the Druid tradition.',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'wild-shape',
                'label' => 'Wild Shape',
                'detail' =>
                    'The Druid gains the Calling’s signature transformation feature.',
            ],
            [
                'key' => 'druid-circle',
                'label' => 'Druid Circle',
                'detail' =>
                    'At Level 2 the Druid joins one of the six registered Great Marketrealm Circles.',
            ],
        ],
        4 => [
            [
                'key' => 'wild-shape-improvement',
                'label' => 'Wild Shape Improvement',
                'detail' =>
                    'Wild Shape expands to stronger forms as the Druid’s transformation craft deepens.',
            ],
        ],
        8 => [
            [
                'key' => 'wild-shape-improvement',
                'label' => 'Wild Shape Improvement',
                'detail' =>
                    'Wild Shape reaches its later core transformation threshold.',
            ],
        ],
        18 => [
            [
                'key' => 'timeless-body',
                'label' => 'Timeless Body',
                'detail' =>
                    'The Druid’s bond with the natural cycle slows the ordinary passage of age.',
            ],
            [
                'key' => 'beast-spells',
                'label' => 'Beast Spells',
                'detail' =>
                    'The Druid can weave much of their spellcasting through Wild Shape.',
            ],
        ],
        20 => [
            [
                'key' => 'archdruid',
                'label' => 'Archdruid',
                'detail' =>
                    'At the height of the Calling, Wild Shape and nature magic become almost effortless.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        4 => [
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        6 => [
            [
                'key' => 'circle-gift',
                'folio' => 'path-gifts',
                'label' => 'Circle Gift',
                'detail' =>
                    'The chosen Druid Circle grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.10B',
            ],
        ],
        8 => [
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        10 => [
            [
                'key' => 'circle-gift',
                'folio' => 'path-gifts',
                'label' => 'Circle Gift',
                'detail' =>
                    'The chosen Druid Circle grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.10B',
            ],
        ],
        12 => [
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        14 => [
            [
                'key' => 'circle-gift',
                'folio' => 'path-gifts',
                'label' => 'Circle Gift',
                'detail' =>
                    'The chosen Druid Circle grants its final specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.10B',
            ],
        ],
        16 => [
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        19 => [
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
    ];

    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'druid';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        $this->guard($class);

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'druid',
            'label' => $class->label(),
            'level' => $level,
            'automatic' =>
                self::AUTOMATIC[$level]
                ?? [],
            'delegated' =>
                self::DELEGATED[$level]
                ?? [],
            'catalogue_status' => 'reference',
        ];
    }

    private function guard(
        CharacterClass $class
    ): void {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Druid progression cannot resolve another Calling.'
            );
        }
    }
}
