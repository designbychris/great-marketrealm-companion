<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist advancement reference for the Cleric Calling.
 *
 * III.12.11 establishes the permanent Cleric spine. Domain gifts, Channel
 * Divinity expenditure and active Divine Arts remain later Cleric slices.
 */
final class ClericProgression implements ClassProgressionDefinitionInterface
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
                    'The Cleric is a Wisdom-based full caster who prepares sacred magic from the Cleric tradition.',
            ],
            [
                'key' => 'divine-domain',
                'label' => 'Divine Domain',
                'detail' =>
                    'At Level 1 the Cleric enters one of the six registered Great Marketrealm Divine Domains.',
            ],
        ];
    }

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const AUTOMATIC = [
        2 => [
            [
                'key' => 'channel-divinity',
                'label' => 'Channel Divinity',
                'detail' =>
                    'The Cleric gains one use of Channel Divinity per short or long rest.',
            ],
            [
                'key' => 'turn-undead',
                'label' => 'Channel Divinity: Turn Undead',
                'detail' =>
                    'The Cleric can present their holy symbol and turn nearby undead through Channel Divinity.',
            ],
        ],
        5 => [
            [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead',
                'detail' =>
                    'Turn Undead destroys sufficiently weak undead that fail their saving throw.',
                'threshold' => 'CR 1/2',
            ],
        ],
        6 => [
            [
                'key' => 'channel-divinity-improvement',
                'label' => 'Channel Divinity Improvement',
                'detail' =>
                    'Channel Divinity increases to two uses between rests.',
            ],
        ],
        8 => [
            [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead Improvement',
                'detail' =>
                    'Destroy Undead threshold improves.',
                'threshold' => 'CR 1',
            ],
        ],
        10 => [
            [
                'key' => 'divine-intervention',
                'label' => 'Divine Intervention',
                'detail' =>
                    'The Cleric may call directly upon their divine power for extraordinary aid.',
            ],
        ],
        11 => [
            [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead Improvement',
                'detail' =>
                    'Destroy Undead threshold improves.',
                'threshold' => 'CR 2',
            ],
        ],
        14 => [
            [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead Improvement',
                'detail' =>
                    'Destroy Undead threshold improves.',
                'threshold' => 'CR 3',
            ],
        ],
        17 => [
            [
                'key' => 'destroy-undead',
                'label' => 'Destroy Undead Improvement',
                'detail' =>
                    'Destroy Undead threshold improves.',
                'threshold' => 'CR 4',
            ],
        ],
        18 => [
            [
                'key' => 'channel-divinity-improvement',
                'label' => 'Channel Divinity Improvement',
                'detail' =>
                    'Channel Divinity increases to three uses between rests.',
            ],
        ],
        20 => [
            [
                'key' => 'divine-intervention-improvement',
                'label' => 'Divine Intervention Improvement',
                'detail' =>
                    'The Cleric’s Divine Intervention reaches its final Calling threshold.',
            ],
        ],
    ];

    /**
     * @var array<int,array<int,array<string,mixed>>>
     */
    private const DELEGATED = [
        2 => [
            [
                'key' => 'domain-gift',
                'folio' => 'path-gifts',
                'label' => 'Domain Gift',
                'detail' =>
                    'The chosen Divine Domain grants its next specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.11B',
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
                'key' => 'domain-gift',
                'folio' => 'path-gifts',
                'label' => 'Domain Gift',
                'detail' =>
                    'The chosen Divine Domain grants its next specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.11B',
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
            [
                'key' => 'domain-gift',
                'folio' => 'path-gifts',
                'label' => 'Domain Gift',
                'detail' =>
                    'The chosen Divine Domain grants its Level 8 specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.11B',
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
                'key' => 'domain-gift',
                'folio' => 'path-gifts',
                'label' => 'Final Domain Gift',
                'detail' =>
                    'The chosen Divine Domain grants its final specialist feature through the shared Path Gifts framework.',
                'phase' => 'III.12.11B',
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
        return $class->value() === 'cleric';
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
            'class' => 'cleric',
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
                'Cleric progression cannot resolve another Calling.'
            );
        }
    }
}
