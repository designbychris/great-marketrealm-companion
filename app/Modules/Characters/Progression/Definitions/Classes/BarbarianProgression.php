<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Barbarian Calling.
 *
 * The repository already registers Rage as a Barbarian feature and eight
 * Barbarian Paths in the player catalogue. This progression connects those
 * existing identities to GMRC's shared advancement machinery.
 *
 * Active Rage expenditure/state is intentionally left to III.12.3C.
 */
final class BarbarianProgression implements ClassProgressionDefinitionInterface
{
    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'reckless-attack',
                'label' => 'Reckless Attack',
                'detail' =>
                    'The Barbarian can abandon caution for a more aggressive opening in battle.',
            ],
            [
                'key' => 'danger-sense',
                'label' => 'Danger Sense',
                'detail' =>
                    'Battlefield instinct sharpens the Barbarian’s reactions to sudden danger.',
            ],
        ],
        5 => [
            [
                'key' => 'extra-attack',
                'label' => 'Extra Attack',
                'detail' =>
                    'The Barbarian’s Attack action advances to two attacks.',
                'attacks' => 2,
            ],
            [
                'key' => 'fast-movement',
                'label' => 'Fast Movement',
                'detail' =>
                    'The Barbarian’s hardened stride becomes quicker while adventuring.',
            ],
        ],
        7 => [
            [
                'key' => 'feral-instinct',
                'label' => 'Feral Instinct',
                'detail' =>
                    'The Barbarian’s instincts sharpen at the opening of combat.',
            ],
        ],
        9 => [
            [
                'key' => 'brutal-critical',
                'label' => 'Brutal Critical',
                'detail' =>
                    'Critical weapon strikes gain an additional weapon damage die.',
                'extra_dice' => 1,
            ],
        ],
        11 => [
            [
                'key' => 'relentless-rage',
                'label' => 'Relentless Rage',
                'detail' =>
                    'Rage becomes difficult to extinguish even when the Barbarian is driven to the brink.',
            ],
        ],
        13 => [
            [
                'key' => 'brutal-critical',
                'label' => 'Brutal Critical',
                'detail' =>
                    'Brutal Critical advances to two additional weapon damage dice.',
                'extra_dice' => 2,
            ],
        ],
        15 => [
            [
                'key' => 'persistent-rage',
                'label' => 'Persistent Rage',
                'detail' =>
                    'The Barbarian’s Rage becomes far harder to end prematurely.',
            ],
        ],
        17 => [
            [
                'key' => 'brutal-critical',
                'label' => 'Brutal Critical',
                'detail' =>
                    'Brutal Critical advances to three additional weapon damage dice.',
                'extra_dice' => 3,
            ],
        ],
        18 => [
            [
                'key' => 'indomitable-might',
                'label' => 'Indomitable Might',
                'detail' =>
                    'Raw Strength becomes exceptionally dependable when tested.',
            ],
        ],
        20 => [
            [
                'key' => 'primal-champion',
                'label' => 'Primal Champion',
                'detail' =>
                    'The Barbarian reaches the summit of primal physical power.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'primal-path',
                'folio' => 'path',
                'label' => 'Primal Path',
                'detail' =>
                    'Choose the Barbarian Path that shapes this adventurer’s Rage and primal identity.',
                'phase' => 'III.12.3',
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
            [
                'key' => 'barbarian-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Primal Path Feature',
                'detail' =>
                    'The selected Barbarian Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.3B',
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
                'key' => 'barbarian-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Primal Path Feature',
                'detail' =>
                    'The selected Barbarian Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.3B',
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
                'key' => 'barbarian-path-feature',
                'folio' => 'path-gifts',
                'label' => 'Primal Path Feature',
                'detail' =>
                    'The selected Barbarian Path may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.3B',
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
        return $class->value() === 'barbarian';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Barbarian progression cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'barbarian',
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
