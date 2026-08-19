<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Sorcerer Calling.
 *
 * III.12.8 establishes the permanent Sorcerer spine while leaving Origin
 * Gifts, Sorcery Point expenditure, Metamagic choices and active spellcasting
 * to later Sorcerer slices.
 */
final class SorcererProgression implements ClassProgressionDefinitionInterface
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
                    'The Sorcerer begins as a Charisma-based full caster whose magic is known instinctively rather than prepared from a spellbook.',
            ],
            [
                'key' => 'sorcerous-origin',
                'label' => 'Sorcerous Origin',
                'detail' =>
                    'The supernatural source of the Sorcerer’s power is present from Level 1 and defines their specialist path.',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'font-of-magic',
                'label' => 'Font of Magic',
                'detail' =>
                    'The Sorcerer gains Sorcery Points equal to their Sorcerer level and begins converting raw magical power through the Font of Magic.',
                'sorcery_point_maximum' =>
                    'sorcerer-level',
            ],
        ],
        3 => [
            [
                'key' => 'metamagic',
                'label' => 'Metamagic',
                'detail' =>
                    'The Sorcerer learns two Metamagic options that reshape how known spells are cast.',
                'options_known' => 2,
            ],
        ],
        10 => [
            [
                'key' => 'metamagic',
                'label' => 'Metamagic',
                'detail' =>
                    'The Sorcerer learns a third Metamagic option.',
                'options_known' => 3,
            ],
        ],
        17 => [
            [
                'key' => 'metamagic',
                'label' => 'Metamagic',
                'detail' =>
                    'The Sorcerer learns a fourth Metamagic option.',
                'options_known' => 4,
            ],
        ],
        20 => [
            [
                'key' => 'sorcerous-restoration',
                'label' => 'Sorcerous Restoration',
                'detail' =>
                    'At the height of the Calling, a short rest restores a measure of spent Sorcery Points.',
                'restored_points' => 4,
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
                'key' => 'origin-gift',
                'folio' => 'path-gifts',
                'label' => 'Origin Gift',
                'detail' =>
                    'The chosen Sorcerous Origin grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.8B',
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
                'key' => 'origin-gift',
                'folio' => 'path-gifts',
                'label' => 'Origin Gift',
                'detail' =>
                    'The chosen Sorcerous Origin grants its next specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.8B',
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
        18 => [
            [
                'key' => 'origin-gift',
                'folio' => 'path-gifts',
                'label' => 'Origin Gift',
                'detail' =>
                    'The chosen Sorcerous Origin grants its final specialist gift through the shared Path Gifts framework.',
                'phase' => 'III.12.8B',
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
        return $class->value() === 'sorcerer';
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

        $automatic =
            self::AUTOMATIC[$level]
            ?? [];

        return [
            'class' => 'sorcerer',
            'label' => $class->label(),
            'level' => $level,
            'automatic' => $automatic,
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
                'Sorcerer progression cannot resolve another Calling.'
            );
        }
    }
}
