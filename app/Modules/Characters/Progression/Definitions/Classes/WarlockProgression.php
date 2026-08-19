<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Warlock Calling.
 *
 * III.12.7 establishes the permanent Warlock advancement spine while keeping
 * Patron Gifts, Invocation selection, Pact Magic reserves and active spell
 * expenditure in their later dedicated slices.
 */
final class WarlockProgression implements ClassProgressionDefinitionInterface
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
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'The Warlock begins with Charisma-based Pact Magic whose compact spell-slot reserve advances differently from ordinary spellcasting.',
            ],
            [
                'key' => 'otherworldly-patron',
                'label' => 'Otherworldly Patron',
                'detail' =>
                    'The Warlock’s supernatural contract begins at Level 1 and defines their specialist Patron identity.',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock begins shaping their bargain through two Eldritch Invocations.',
                'known' => 2,
            ],
        ],
        3 => [
            [
                'key' => 'pact-boon',
                'label' => 'Pact Boon',
                'detail' =>
                    'The Warlock receives a deeper boon that changes how the pact is expressed in play.',
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'Pact Magic advances to 2nd-level spell slots.',
                'slot_level' => 2,
                'slots' => 2,
            ],
        ],
        5 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire increases to three.',
                'known' => 3,
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'Pact Magic advances to 3rd-level spell slots.',
                'slot_level' => 3,
                'slots' => 2,
            ],
        ],
        7 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire increases to four.',
                'known' => 4,
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'Pact Magic advances to 4th-level spell slots.',
                'slot_level' => 4,
                'slots' => 2,
            ],
        ],
        9 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire increases to five.',
                'known' => 5,
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'Pact Magic advances to 5th-level spell slots.',
                'slot_level' => 5,
                'slots' => 2,
            ],
        ],
        11 => [
            [
                'key' => 'mystic-arcanum-6',
                'label' => 'Mystic Arcanum',
                'detail' =>
                    'The Warlock gains a once-per-long-rest 6th-level Mystic Arcanum.',
                'spell_level' => 6,
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'The Warlock’s Pact Magic reserve expands to three 5th-level slots.',
                'slot_level' => 5,
                'slots' => 3,
            ],
        ],
        12 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire increases to six.',
                'known' => 6,
            ],
        ],
        13 => [
            [
                'key' => 'mystic-arcanum-7',
                'label' => 'Mystic Arcanum',
                'detail' =>
                    'The Warlock gains a once-per-long-rest 7th-level Mystic Arcanum.',
                'spell_level' => 7,
            ],
        ],
        15 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire increases to seven.',
                'known' => 7,
            ],
            [
                'key' => 'mystic-arcanum-8',
                'label' => 'Mystic Arcanum',
                'detail' =>
                    'The Warlock gains a once-per-long-rest 8th-level Mystic Arcanum.',
                'spell_level' => 8,
            ],
        ],
        17 => [
            [
                'key' => 'mystic-arcanum-9',
                'label' => 'Mystic Arcanum',
                'detail' =>
                    'The Warlock gains a once-per-long-rest 9th-level Mystic Arcanum.',
                'spell_level' => 9,
            ],
            [
                'key' => 'pact-magic',
                'label' => 'Pact Magic',
                'detail' =>
                    'The Warlock’s Pact Magic reserve reaches four 5th-level slots.',
                'slot_level' => 5,
                'slots' => 4,
            ],
        ],
        18 => [
            [
                'key' => 'eldritch-invocations',
                'label' => 'Eldritch Invocations',
                'detail' =>
                    'The Warlock’s Invocation repertoire reaches eight.',
                'known' => 8,
            ],
        ],
        20 => [
            [
                'key' => 'eldritch-master',
                'label' => 'Eldritch Master',
                'detail' =>
                    'At the height of the Calling, the Warlock can entreat their Patron to restore spent Pact Magic.',
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
                'key' => 'patron-gift',
                'folio' => 'path-gifts',
                'label' => 'Patron Gift',
                'detail' =>
                    'The chosen Patron grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.7B',
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
                'key' => 'patron-gift',
                'folio' => 'path-gifts',
                'label' => 'Patron Gift',
                'detail' =>
                    'The chosen Patron grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.7B',
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
                'key' => 'patron-gift',
                'folio' => 'path-gifts',
                'label' => 'Patron Gift',
                'detail' =>
                    'The chosen Patron grants its final specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.7B',
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
        return $class->value() === 'warlock';
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
            'class' => 'warlock',
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
                'Warlock progression cannot resolve another Calling.'
            );
        }
    }
}
