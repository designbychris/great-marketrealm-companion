<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Rogue Calling.
 *
 * III.12.4 establishes permanent Rogue progression and Archetype hand-offs.
 * Contextual Sneak Attack, Cunning Action and reaction play belong to later
 * Rogue slices rather than this permanent advancement definition.
 */
final class RogueProgression implements ClassProgressionDefinitionInterface
{
    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'cunning-action',
                'label' => 'Cunning Action',
                'detail' =>
                    'The Rogue can turn speed, positioning and escape into a bonus-action specialty.',
            ],
        ],
        3 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 2d6.',
                'dice' => '2d6',
            ],
        ],
        5 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 3d6.',
                'dice' => '3d6',
            ],
            [
                'key' => 'uncanny-dodge',
                'label' => 'Uncanny Dodge',
                'detail' =>
                    'The Rogue can react defensively to reduce the impact of a visible attacker.',
            ],
        ],
        6 => [
            [
                'key' => 'expertise',
                'label' => 'Expertise',
                'detail' =>
                    'The Rogue deepens mastery in additional trained skills.',
            ],
        ],
        7 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 4d6.',
                'dice' => '4d6',
            ],
            [
                'key' => 'evasion',
                'label' => 'Evasion',
                'detail' =>
                    'The Rogue becomes exceptionally difficult to catch in destructive area effects.',
            ],
        ],
        9 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 5d6.',
                'dice' => '5d6',
            ],
        ],
        11 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 6d6.',
                'dice' => '6d6',
            ],
            [
                'key' => 'reliable-talent',
                'label' => 'Reliable Talent',
                'detail' =>
                    'The Rogue’s trained skills become extraordinarily dependable.',
            ],
        ],
        13 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 7d6.',
                'dice' => '7d6',
            ],
        ],
        14 => [
            [
                'key' => 'blindsense',
                'label' => 'Blindsense',
                'detail' =>
                    'The Rogue’s awareness sharpens against nearby unseen threats.',
            ],
        ],
        15 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 8d6.',
                'dice' => '8d6',
            ],
            [
                'key' => 'slippery-mind',
                'label' => 'Slippery Mind',
                'detail' =>
                    'The Rogue’s mental discipline becomes harder to seize or control.',
            ],
        ],
        17 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack advances to 9d6.',
                'dice' => '9d6',
            ],
        ],
        18 => [
            [
                'key' => 'elusive',
                'label' => 'Elusive',
                'detail' =>
                    'The Rogue becomes extraordinarily difficult to catch at a disadvantage.',
            ],
        ],
        19 => [
            [
                'key' => 'sneak-attack',
                'label' => 'Sneak Attack',
                'detail' =>
                    'Sneak Attack reaches 10d6.',
                'dice' => '10d6',
            ],
        ],
        20 => [
            [
                'key' => 'stroke-of-luck',
                'label' => 'Stroke of Luck',
                'detail' =>
                    'At the height of Rogue mastery, a missed opportunity can become a decisive success.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        3 => [
            [
                'key' => 'rogue-archetype',
                'folio' => 'path',
                'label' => 'Rogue Archetype',
                'detail' =>
                    'Choose the Archetype that shapes this Rogue’s specialised methods and identity.',
                'phase' => 'III.12.4',
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
                'key' => 'rogue-archetype-feature',
                'folio' => 'path-gifts',
                'label' => 'Rogue Archetype Feature',
                'detail' =>
                    'The selected Rogue Archetype may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.4B',
            ],
        ],
        10 => [
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
        13 => [
            [
                'key' => 'rogue-archetype-feature',
                'folio' => 'path-gifts',
                'label' => 'Rogue Archetype Feature',
                'detail' =>
                    'The selected Rogue Archetype may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.4B',
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
                'key' => 'rogue-archetype-feature',
                'folio' => 'path-gifts',
                'label' => 'Rogue Archetype Feature',
                'detail' =>
                    'The selected Rogue Archetype may grant its next specialist feature through the Path Gifts framework.',
                'phase' => 'III.12.4B',
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
        return $class->value() === 'rogue';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Rogue progression cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'rogue',
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
