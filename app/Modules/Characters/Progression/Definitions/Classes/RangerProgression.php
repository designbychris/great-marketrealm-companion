<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Ranger Calling.
 *
 * III.12.9 certifies the Ranger's permanent class spine while keeping Ranger
 * subclass/path work outside the progression catalogue until the repository
 * actually contains Ranger subclass candidates.
 */
final class RangerProgression implements ClassProgressionDefinitionInterface
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
                'key' => 'favoured-mark',
                'label' => 'Favoured Mark',
                'detail' =>
                    'Study a quarry and mark the signs needed to track it through the Marketrealm.',
            ],
            [
                'key' => 'natural-explorer',
                'label' => 'Natural Explorer',
                'detail' =>
                    'The Ranger is trained to travel, scout and survive through difficult territory.',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'fighting-style',
                'label' => 'Fighting Style',
                'detail' =>
                    'The Ranger adopts a specialised martial fighting style.',
            ],
            [
                'key' => 'spellcasting',
                'label' => 'Spellcasting',
                'detail' =>
                    'The Ranger gains Wisdom-based half-caster spellcasting using known spells.',
            ],
        ],
        3 => [
            [
                'key' => 'primeval-awareness',
                'label' => 'Primeval Awareness',
                'detail' =>
                    'The Ranger develops a supernatural awareness of unusual creatures and threats in the surrounding wilds.',
            ],
        ],
        5 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Ranger can attack twice, instead of once, when taking the Attack action.',
            ],
        ],
        6 => [
            [
                'key' => 'favoured-mark-improvement',
                'label' => 'Favoured Mark Improvement',
                'detail' =>
                    'The Ranger’s quarry knowledge and tracking expertise deepen.',
            ],
        ],
        8 => [
            [
                'key' => 'lands-stride',
                'label' => 'Land’s Stride',
                'detail' =>
                    'The Ranger moves through difficult natural terrain with practiced confidence.',
            ],
        ],
        10 => [
            [
                'key' => 'hide-in-plain-sight',
                'label' => 'Hide in Plain Sight',
                'detail' =>
                    'The Ranger’s fieldcraft makes concealment in the wilds substantially more effective.',
            ],
        ],
        14 => [
            [
                'key' => 'vanish',
                'label' => 'Vanish',
                'detail' =>
                    'The Ranger becomes exceptionally difficult to pin down while moving through danger.',
            ],
            [
                'key' => 'favoured-mark-improvement',
                'label' => 'Favoured Mark Improvement',
                'detail' =>
                    'The Ranger’s quarry expertise reaches its final major expansion.',
            ],
        ],
        18 => [
            [
                'key' => 'feral-senses',
                'label' => 'Feral Senses',
                'detail' =>
                    'The Ranger’s awareness can locate threats that ordinary sight would miss.',
            ],
        ],
        20 => [
            [
                'key' => 'foe-slayer',
                'label' => 'Foe Slayer',
                'detail' =>
                    'At the height of the Calling, the Ranger’s practiced instincts turn quarry knowledge into decisive precision.',
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
        return $class->value() === 'ranger';
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
            'class' => 'ranger',
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
                'Ranger progression cannot resolve another Calling.'
            );
        }
    }
}
