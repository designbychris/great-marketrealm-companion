<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Fighter Calling.
 *
 * Automatic entries describe progression owned by the Calling. They are
 * deliberately reference metadata in III.12.2; certification does not invent
 * a new permanent feature store.
 *
 * Decisions owned by another folio are delegated so the shared advancement
 * machinery remains class-agnostic.
 */
final class FighterProgression implements ClassProgressionDefinitionInterface
{
    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'action-surge',
                'label' => 'Action Surge',
                'detail' =>
                    'The Fighter can dig deeper in battle and take an additional action.',
            ],
        ],
        5 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Fighter’s martial training supports a second attack when taking the Attack action.',
                'attacks' => 2,
            ],
        ],
        9 => [
            [
                'key' => 'indomitable',
                'label' => 'Indomitable',
                'detail' =>
                    'The Fighter gains a hardened reserve for recovering from a failed saving throw.',
                'uses' => 1,
            ],
        ],
        11 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Fighter’s Attack action advances to three attacks.',
                'attacks' => 3,
            ],
        ],
        13 => [
            [
                'key' => 'indomitable',
                'label' => 'Indomitable',
                'detail' =>
                    'The Fighter’s Indomitable reserve increases.',
                'uses' => 2,
            ],
        ],
        17 => [
            [
                'key' => 'action-surge',
                'label' => 'Action Surge',
                'detail' =>
                    'The Fighter can call on Action Surge twice between rests.',
                'uses' => 2,
            ],
            [
                'key' => 'indomitable',
                'label' => 'Indomitable',
                'detail' =>
                    'The Fighter’s Indomitable reserve increases again.',
                'uses' => 3,
            ],
        ],
        20 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Fighter’s Attack action reaches four attacks.',
                'attacks' => 4,
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'martial-path',
                'folio' => 'path',
                'label' => 'Martial Path',
                'detail' =>
                    'Choose the Fighter path that shapes this adventurer’s specialised martial training.',
                'phase' => 'III.12.2',
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
                'key' => 'fighter-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Martial Path Feature',
                'detail' =>
                    'The selected Fighter Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.2B',
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
                'key' => 'fighter-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Martial Path Feature',
                'detail' =>
                    'The selected Fighter Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.2B',
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
                'key' => 'fighter-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Martial Path Feature',
                'detail' =>
                    'The selected Fighter Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.2B',
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
                'key' => 'fighter-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Martial Path Feature',
                'detail' =>
                    'The selected Fighter Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.2B',
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
        return $class->value() === 'fighter';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Fighter progression cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'fighter',
            'label' => $class->label(),
            'level' => $level,
            'automatic' =>
                self::AUTOMATIC[$level] ?? [],
            'delegated' =>
                self::DELEGATED[$level] ?? [],
            'catalogue_status' => 'reference',
        ];
    }
}
