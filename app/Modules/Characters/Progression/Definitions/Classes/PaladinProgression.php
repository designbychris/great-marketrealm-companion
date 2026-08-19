<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Paladin Calling.
 *
 * III.12.6 establishes the permanent 1–20 Paladin advancement spine.
 * Sacred Oath candidates, active reserves, spell-slot certification and
 * interactive sacred actions belong to the following Paladin slices.
 */
final class PaladinProgression implements ClassProgressionDefinitionInterface
{
    /**
     * Level-one features are creation-time Calling foundations.
     *
     * @return array<int,array<string,mixed>>
     */
    public function foundations(
        CharacterClass $class
    ): array {
        $this->guard($class);

        return [
            [
                'key' => 'divine-sense',
                'label' => 'Divine Sense',
                'detail' =>
                    'The Paladin can open their awareness to qualifying sacred, profane and spoiled presences nearby.',
            ],
            [
                'key' => 'lay-on-hands',
                'label' => 'Lay on Hands',
                'detail' =>
                    'The Paladin begins with a level-scaled pool of restorative sacred power.',
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
                    'The Paladin adopts a specialised martial discipline.',
            ],
            [
                'key' => 'spellcasting',
                'label' => 'Spellcasting',
                'detail' =>
                    'The Paladin begins preparing and casting sacred magic through a half-caster progression.',
            ],
            [
                'key' => 'divine-smite',
                'label' => 'Divine Smite',
                'detail' =>
                    'The Paladin can commit qualifying spell-slot power to a successful melee weapon strike for radiant damage.',
            ],
        ],
        3 => [
            [
                'key' => 'divine-health',
                'label' => 'Divine Health',
                'detail' =>
                    'Sacred conviction hardens the Paladin against disease.',
            ],
        ],
        5 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Paladin can attack twice when taking the Attack action.',
                'attacks' => 2,
            ],
        ],
        6 => [
            [
                'key' => 'aura-of-protection',
                'label' => 'Aura of Protection',
                'detail' =>
                    'The Paladin’s presence bolsters qualifying saving throws for the Paladin and nearby allies.',
                'range_feet' => 10,
            ],
        ],
        10 => [
            [
                'key' => 'aura-of-courage',
                'label' => 'Aura of Courage',
                'detail' =>
                    'The Paladin and nearby allies are protected from fear while within the sacred aura.',
                'range_feet' => 10,
            ],
        ],
        11 => [
            [
                'key' => 'improved-divine-smite',
                'label' => 'Improved Divine Smite',
                'detail' =>
                    'The Paladin’s melee weapon strikes carry an enduring measure of radiant power.',
            ],
        ],
        14 => [
            [
                'key' => 'cleansing-touch',
                'label' => 'Cleansing Touch',
                'detail' =>
                    'The Paladin gains a limited sacred action for ending qualifying magical effects.',
            ],
        ],
        18 => [
            [
                'key' => 'aura-improvement',
                'label' => 'Aura Improvement',
                'detail' =>
                    'The Paladin’s qualifying sacred auras extend to a greater distance.',
                'range_feet' => 30,
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'sacred-oath',
                'folio' => 'path',
                'label' => 'Sacred Oath',
                'detail' =>
                    'Choose the Sacred Oath that defines this Paladin’s vows, duties and specialist identity.',
                'phase' => 'III.12.6B',
            ],
            [
                'key' => 'sacred-oath-gift',
                'folio' => 'path-gifts',
                'label' => 'Sacred Oath Gift',
                'detail' =>
                    'The chosen Sacred Oath grants its first specialist gifts.',
                'phase' => 'III.12.6B',
            ],
        ],
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
        7 => [
            [
                'key' => 'sacred-oath-feature',
                'folio' => 'path-gifts',
                'label' => 'Sacred Oath Feature',
                'detail' =>
                    'The selected Sacred Oath grants its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.6B',
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
        15 => [
            [
                'key' => 'sacred-oath-feature',
                'folio' => 'path-gifts',
                'label' => 'Sacred Oath Feature',
                'detail' =>
                    'The selected Sacred Oath grants its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.6B',
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
        20 => [
            [
                'key' => 'sacred-oath-capstone',
                'folio' => 'path-gifts',
                'label' => 'Sacred Oath Capstone',
                'detail' =>
                    'The selected Sacred Oath grants its final specialist transformation or capstone.',
                'phase' => 'III.12.6B',
            ],
        ],
    ];

    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'paladin';
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
            'class' => 'paladin',
            'label' => $class->label(),
            'level' => $level,
            'automatic' =>
                self::AUTOMATIC[$level] ?? [],
            'delegated' =>
                self::DELEGATED[$level] ?? [],
            'catalogue_status' => 'reference',
        ];
    }

    private function guard(
        CharacterClass $class
    ): void {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Paladin progression cannot resolve another Calling.'
            );
        }
    }
}
