<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Monk Calling.
 *
 * III.12.5 establishes permanent Monk progression and the Way hand-off.
 * Discipline reserves and active techniques belong to later Monk slices.
 */
final class MonkProgression implements ClassProgressionDefinitionInterface
{
    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'discipline',
                'label' => 'Discipline',
                'detail' =>
                    'The Monk gains a level-scaled pool of inner discipline used to fuel specialist techniques.',
            ],
            [
                'key' => 'unarmoured-movement',
                'label' => 'Unarmoured Movement',
                'detail' =>
                    'The Monk’s trained movement becomes faster while fighting unencumbered.',
            ],
        ],
        3 => [
            [
                'key' => 'deflect-missiles',
                'label' => 'Deflect Missiles',
                'detail' =>
                    'The Monk learns to turn trained reactions against incoming ranged weapon attacks.',
            ],
        ],
        4 => [
            [
                'key' => 'slow-fall',
                'label' => 'Slow Fall',
                'detail' =>
                    'The Monk can use trained control to reduce dangerous falling impact.',
            ],
        ],
        5 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Attack action advances to two attacks.',
            ],
            [
                'key' => 'stunning-strike',
                'label' => 'Stunning Strike',
                'detail' =>
                    'The Monk can channel discipline through a successful melee strike to attempt a disabling opening.',
            ],
        ],
        6 => [
            [
                'key' => 'disciplined-strikes',
                'label' => 'Disciplined Strikes',
                'detail' =>
                    'The Monk’s unarmed and specialist strikes become capable of overcoming unusual resistance.',
            ],
        ],
        7 => [
            [
                'key' => 'evasion',
                'label' => 'Evasion',
                'detail' =>
                    'The Monk’s reflex training greatly improves survival against qualifying Dexterity-save area effects.',
            ],
            [
                'key' => 'stillness-of-mind',
                'label' => 'Stillness of Mind',
                'detail' =>
                    'The Monk can use disciplined focus to break certain effects that seize the mind.',
            ],
        ],
        10 => [
            [
                'key' => 'purity-of-body',
                'label' => 'Purity of Body',
                'detail' =>
                    'Years of training harden the Monk against bodily corruption and sickness.',
            ],
        ],
        13 => [
            [
                'key' => 'tongue-of-sun-and-moon',
                'label' => 'Tongue of Sun and Moon',
                'detail' =>
                    'The Monk’s disciplined awareness allows communication across extraordinary linguistic boundaries.',
            ],
        ],
        14 => [
            [
                'key' => 'diamond-soul',
                'label' => 'Diamond Soul',
                'detail' =>
                    'The Monk’s inner discipline fortifies every saving throw.',
            ],
        ],
        15 => [
            [
                'key' => 'timeless-body',
                'label' => 'Timeless Body',
                'detail' =>
                    'The Monk’s mastery loosens the ordinary demands of age and sustenance.',
            ],
        ],
        18 => [
            [
                'key' => 'empty-body',
                'label' => 'Empty Body',
                'detail' =>
                    'The Monk can spend deep reserves of discipline to enter an extraordinary state beyond ordinary sight.',
            ],
        ],
        20 => [
            [
                'key' => 'perfect-self',
                'label' => 'Perfect Self',
                'detail' =>
                    'At the height of mastery, the Monk can recover a small reserve of discipline when battle begins empty.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'monastic-way',
                'folio' => 'path',
                'label' => 'Monastic Way',
                'detail' =>
                    'Choose the Way that shapes this Monk’s discipline, movement and martial identity.',
                'phase' => 'III.12.5',
            ],
            [
                'key' => 'monastic-way-gift',
                'folio' => 'path-gifts',
                'label' => 'Monastic Way Gift',
                'detail' =>
                    'The chosen Monastic Way grants its first specialist gift.',
                'phase' => 'III.12.5B',
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
                'key' => 'monastic-way-feature',
                'folio' => 'path-gifts',
                'label' => 'Monastic Way Feature',
                'detail' =>
                    'The selected Monastic Way may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.5B',
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
        11 => [
            [
                'key' => 'monastic-way-feature',
                'folio' => 'path-gifts',
                'label' => 'Monastic Way Feature',
                'detail' =>
                    'The selected Monastic Way may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.5B',
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
        17 => [
            [
                'key' => 'monastic-way-feature',
                'folio' => 'path-gifts',
                'label' => 'Monastic Way Feature',
                'detail' =>
                    'The selected Monastic Way may grant its final specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.5B',
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
        return $class->value() === 'monk';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Monk progression cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'monk',
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
