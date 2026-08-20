<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Bard Calling.
 *
 * III.12.12 establishes the permanent Bard spine. College Gifts, Bardic
 * Inspiration expenditure and player-facing Bard Arts remain later slices.
 */
final class BardProgression implements ClassProgressionDefinitionInterface
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
                'key' => 'spellcasting',
                'label' => 'Spellcasting',
                'detail' =>
                    'The Bard is a Charisma-based full caster who learns a flexible repertoire of spells.',
            ],
            [
                'key' => 'bardic-inspiration',
                'label' => 'Bardic Inspiration',
                'detail' =>
                    'The Bard begins with a d6 Inspiration die and a number of uses tied to Charisma.',
                'die' => 'd6',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'jack-of-all-trades',
                'label' => 'Jack of All Trades',
                'detail' =>
                    'Add half the Bard’s proficiency bonus, rounded down, to ability checks that do not already include proficiency.',
            ],
            [
                'key' => 'song-of-rest',
                'label' => 'Song of Rest',
                'detail' =>
                    'During a short rest, the Bard’s performance helps allies recover additional hit points.',
                'die' => 'd6',
            ],
        ],
        3 => [
            [
                'key' => 'expertise',
                'label' => 'Expertise',
                'detail' =>
                    'Choose two skill proficiencies to receive doubled proficiency.',
                'choices' => 2,
            ],
        ],
        5 => [
            [
                'key' => 'bardic-inspiration-improvement',
                'label' => 'Bardic Inspiration Improvement',
                'detail' =>
                    'The Bardic Inspiration die becomes d8.',
                'die' => 'd8',
            ],
            [
                'key' => 'font-of-inspiration',
                'label' => 'Font of Inspiration',
                'detail' =>
                    'Bardic Inspiration now refreshes after a short or long rest.',
            ],
        ],
        6 => [
            [
                'key' => 'countercharm',
                'label' => 'Countercharm',
                'detail' =>
                    'The Bard can perform to help nearby allies resist frightening and charming effects.',
            ],
        ],
        9 => [
            [
                'key' => 'song-of-rest-improvement',
                'label' => 'Song of Rest Improvement',
                'detail' =>
                    'Song of Rest improves to d8.',
                'die' => 'd8',
            ],
        ],
        10 => [
            [
                'key' => 'bardic-inspiration-improvement',
                'label' => 'Bardic Inspiration Improvement',
                'detail' =>
                    'The Bardic Inspiration die becomes d10.',
                'die' => 'd10',
            ],
            [
                'key' => 'expertise',
                'label' => 'Expertise',
                'detail' =>
                    'Choose two additional skill proficiencies to receive doubled proficiency.',
                'choices' => 2,
            ],
            [
                'key' => 'magical-secrets',
                'label' => 'Magical Secrets',
                'detail' =>
                    'Learn two spells chosen from the spell lists of any Calling.',
                'choices' => 2,
            ],
        ],
        13 => [
            [
                'key' => 'song-of-rest-improvement',
                'label' => 'Song of Rest Improvement',
                'detail' =>
                    'Song of Rest improves to d10.',
                'die' => 'd10',
            ],
        ],
        14 => [
            [
                'key' => 'magical-secrets',
                'label' => 'Magical Secrets',
                'detail' =>
                    'Learn two additional spells chosen from the spell lists of any Calling.',
                'choices' => 2,
            ],
        ],
        15 => [
            [
                'key' => 'bardic-inspiration-improvement',
                'label' => 'Bardic Inspiration Improvement',
                'detail' =>
                    'The Bardic Inspiration die becomes d12.',
                'die' => 'd12',
            ],
        ],
        17 => [
            [
                'key' => 'song-of-rest-improvement',
                'label' => 'Song of Rest Improvement',
                'detail' =>
                    'Song of Rest improves to d12.',
                'die' => 'd12',
            ],
        ],
        18 => [
            [
                'key' => 'magical-secrets',
                'label' => 'Magical Secrets',
                'detail' =>
                    'Learn two final spells chosen from the spell lists of any Calling.',
                'choices' => 2,
            ],
        ],
        20 => [
            [
                'key' => 'superior-inspiration',
                'label' => 'Superior Inspiration',
                'detail' =>
                    'At the height of the Calling, entering battle without Bardic Inspiration restores one use.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'college-gift',
                'folio' => 'path-gifts',
                'label' => 'College Gift',
                'detail' =>
                    'The chosen Bard College grants its first specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.12B',
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
        6 => [
            [
                'key' => 'college-gift',
                'folio' => 'path-gifts',
                'label' => 'College Gift',
                'detail' =>
                    'The chosen Bard College grants its next specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.12B',
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
        14 => [
            [
                'key' => 'college-gift',
                'folio' => 'path-gifts',
                'label' => 'Final College Gift',
                'detail' =>
                    'The chosen Bard College grants its final specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.12B',
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
        return $class->value() === 'bard';
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
            'class' => 'bard',
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
                'Bard progression cannot resolve another Calling.'
            );
        }
    }
}
