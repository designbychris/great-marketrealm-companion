<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Artificer Calling.
 *
 * III.12.13 establishes the permanent Artificer spine. Specialisation
 * identities and their Gifts remain delegated to the dedicated follow-up
 * slices so the class definition never duplicates subclass mechanics.
 */
final class ArtificerProgression implements ClassProgressionDefinitionInterface
{
    /** @return array<int,array<string,mixed>> */
    public function foundations(CharacterClass $class): array
    {
        $this->guard($class);

        return [
            [
                'key' => 'magical-tinkering',
                'label' => 'Magical Tinkering',
                'detail' =>
                    'The Artificer imbues tiny mundane objects with minor magical effects through practical arcane craft.',
            ],
            [
                'key' => 'spellcasting',
                'label' => 'Spellcasting',
                'detail' =>
                    'The Artificer prepares Intelligence-based spells through tools, inventions and crafted magical workings.',
            ],
        ];
    }

    /** @var array<int,array<int,array<string,mixed>>> */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'infuse-item',
                'label' => 'Infuse Item',
                'detail' =>
                    'The Artificer learns to imbue mundane objects with repeatable magical infusions.',
            ],
        ],
        3 => [
            [
                'key' => 'right-tool-for-the-job',
                'label' => 'The Right Tool for the Job',
                'detail' =>
                    'The Artificer can use artisan knowledge and magic to produce the tools needed for the work ahead.',
            ],
        ],
        6 => [
            [
                'key' => 'tool-expertise',
                'label' => 'Tool Expertise',
                'detail' =>
                    'The Artificer’s proficiency bonus is doubled for ability checks made with proficient tools.',
            ],
        ],
        7 => [
            [
                'key' => 'flash-of-genius',
                'label' => 'Flash of Genius',
                'detail' =>
                    'A sudden stroke of inventive brilliance can improve an ability check or saving throw nearby.',
            ],
        ],
        10 => [
            [
                'key' => 'magic-item-adept',
                'label' => 'Magic Item Adept',
                'detail' =>
                    'The Artificer becomes exceptionally efficient at working with and attuning to magical items.',
            ],
        ],
        11 => [
            [
                'key' => 'spell-storing-item',
                'label' => 'Spell-Storing Item',
                'detail' =>
                    'The Artificer can store a prepared spell inside an object for repeated use through that crafted vessel.',
            ],
        ],
        14 => [
            [
                'key' => 'magic-item-savant',
                'label' => 'Magic Item Savant',
                'detail' =>
                    'The Artificer’s mastery of magical items expands beyond the limitations faced by ordinary adventurers.',
            ],
        ],
        18 => [
            [
                'key' => 'magic-item-master',
                'label' => 'Magic Item Master',
                'detail' =>
                    'The Artificer reaches extraordinary mastery over the use and attunement of magical items.',
            ],
        ],
        20 => [
            [
                'key' => 'soul-of-artifice',
                'label' => 'Soul of Artifice',
                'detail' =>
                    'At the height of the Calling, the Artificer’s bond with crafted magic becomes a final source of resilience and power.',
            ],
        ],
    ];

    /** @var array<int,array<int,array<string,mixed>>> */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'artificer-specialisation-gift',
                'folio' => 'path-gifts',
                'label' => 'Artificer Specialisation Gift',
                'detail' =>
                    'The chosen Artificer Specialisation grants its first specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.13B',
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
        5 => [
            [
                'key' => 'artificer-specialisation-gift',
                'folio' => 'path-gifts',
                'label' => 'Artificer Specialisation Gift',
                'detail' =>
                    'The chosen Artificer Specialisation grants its next specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.13B',
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
        9 => [
            [
                'key' => 'artificer-specialisation-gift',
                'folio' => 'path-gifts',
                'label' => 'Artificer Specialisation Gift',
                'detail' =>
                    'The chosen Artificer Specialisation grants another specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.13B',
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
                'key' => 'artificer-specialisation-gift',
                'folio' => 'path-gifts',
                'label' => 'Final Artificer Specialisation Gift',
                'detail' =>
                    'The chosen Artificer Specialisation grants its final supplied specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.13B',
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

    public function supports(CharacterClass $class): bool
    {
        return $class->value() === 'artificer';
    }

    /** @return array<string,mixed> */
    public function forLevel(CharacterClass $class, int $level): array
    {
        $this->guard($class);

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'artificer',
            'label' => $class->label(),
            'level' => $level,
            'automatic' => self::AUTOMATIC[$level] ?? [],
            'delegated' => self::DELEGATED[$level] ?? [],
            'catalogue_status' => 'reference',
        ];
    }

    private function guard(CharacterClass $class): void
    {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Artificer progression cannot resolve another Calling.'
            );
        }
    }
}
