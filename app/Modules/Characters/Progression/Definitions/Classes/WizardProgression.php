<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class WizardProgression implements ClassProgressionDefinitionInterface
{
    /**
     * Calling milestones owned by later specialist folios.
     *
     * The Calling catalogue identifies these requirements but deliberately
     * does not collect the choice itself. That keeps Spellbook, Path and
     * Measure-of-Growth decisions in their own future phases.
     *
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        2 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning and spellbook progression belong to The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'arcane-tradition',
                'folio' => 'path',
                'label' => 'Arcane Tradition',
                'detail' =>
                    'The Wizard chooses an Arcane Tradition through The Paths of Calling.',
                'phase' => 'III.8.8',
            ],
            [
                'key' => 'arcane-tradition-gifts',
                'folio' => 'path-gifts',
                'label' => 'Gifts of the Path',
                'detail' =>
                    'The chosen Arcane Tradition may grant immediate features through The Gifts of the Path.',
                'phase' => 'III.8.9',
            ],
        ],
        3 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        4 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Cantrip and spellbook progression belong to The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
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
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        6 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'arcane-tradition-feature',
                'folio' => 'path-gifts',
                'label' => 'Arcane Tradition Feature',
                'detail' =>
                    'The selected Arcane Tradition grants its next feature through The Gifts of the Path.',
                'phase' => 'III.8.9',
            ],
        ],
        7 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        8 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
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
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        10 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Cantrip and spellbook progression belong to The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'arcane-tradition-feature',
                'folio' => 'path-gifts',
                'label' => 'Arcane Tradition Feature',
                'detail' =>
                    'The selected Arcane Tradition grants its next feature through The Gifts of the Path.',
                'phase' => 'III.8.9',
            ],
        ],
        11 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        12 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        13 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        14 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'arcane-tradition-feature',
                'folio' => 'path-gifts',
                'label' => 'Arcane Tradition Feature',
                'detail' =>
                    'The selected Arcane Tradition grants its next feature through The Gifts of the Path.',
                'phase' => 'III.8.9',
            ],
        ],
        15 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        16 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
            [
                'key' => 'measure-of-growth',
                'folio' => 'growth',
                'label' => 'Measure of Growth',
                'detail' =>
                    'Ability improvement or talent selection belongs to The Measure of Growth.',
                'phase' => 'III.8.10',
            ],
        ],
        17 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'The spellbook expands and higher-circle arcana become available through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        18 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Spell Mastery',
                'detail' =>
                    'Spell Mastery and its selected spells belong to The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
        19 => [
            [
                'key' => 'arcane-studies',
                'folio' => 'spellbook',
                'label' => 'Arcane Studies',
                'detail' =>
                    'Wizard spell learning continues through The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
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
                'key' => 'signature-spells',
                'folio' => 'spellbook',
                'label' => 'Signature Spells',
                'detail' =>
                    'Signature Spells and their selected spells belong to The Spellbook Folios.',
                'phase' => 'III.8.7',
            ],
        ],
    ];

    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'wizard';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Wizard progression cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => $class->value(),
            'label' => $class->label(),
            'level' => $level,

            /*
             * No permanent class-feature mutation is invented here.
             * Automatic gains will be populated as those feature models are
             * introduced. For now the Calling catalogue identifies which
             * specialist folios own the Wizard's level-specific decisions.
             */
            'automatic' => [],
            'delegated' =>
                self::DELEGATED[$level] ?? [],
            'catalogue_status' => 'reference',
        ];
    }
}
