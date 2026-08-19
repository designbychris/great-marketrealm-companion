<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Certified Metamagic option reference.
 */
final class SorcererMetamagicCatalogue
{
    /**
     * @var array<string,array<string,mixed>>
     */
    private const OPTIONS = [
        'careful-spell' => [
            'label' => 'Careful Spell',
            'cost' => 1,
            'summary' =>
                'Protect chosen creatures from the worst of a spell that forces a saving throw.',
            'timing' =>
                'When casting a spell that forces other creatures to make a saving throw.',
        ],
        'distant-spell' => [
            'label' => 'Distant Spell',
            'cost' => 1,
            'summary' =>
                'Extend a ranged spell or give a touch spell extra reach.',
            'timing' =>
                'When casting a spell whose range can be extended.',
        ],
        'empowered-spell' => [
            'label' => 'Empowered Spell',
            'cost' => 1,
            'summary' =>
                'Reroll a limited number of damage dice using the Sorcerer’s Charisma.',
            'timing' =>
                'After rolling damage for a spell.',
        ],
        'extended-spell' => [
            'label' => 'Extended Spell',
            'cost' => 1,
            'summary' =>
                'Double the duration of a qualifying spell, subject to the normal duration cap.',
            'timing' =>
                'When casting a spell with a qualifying duration.',
        ],
        'heightened-spell' => [
            'label' => 'Heightened Spell',
            'cost' => 3,
            'summary' =>
                'Make one target more vulnerable to the first saving throw imposed by the spell.',
            'timing' =>
                'When casting a spell that forces a saving throw.',
        ],
        'quickened-spell' => [
            'label' => 'Quickened Spell',
            'cost' => 2,
            'summary' =>
                'Change a qualifying one-action spell to a bonus-action casting.',
            'timing' =>
                'When casting a spell with a casting time of one action.',
        ],
        'subtle-spell' => [
            'label' => 'Subtle Spell',
            'cost' => 1,
            'summary' =>
                'Cast without verbal or somatic components.',
            'timing' =>
                'When casting a spell with verbal or somatic components.',
        ],
        'twinned-spell' => [
            'label' => 'Twinned Spell',
            'cost' => 'spell-level',
            'summary' =>
                'Direct a qualifying single-target spell at a second creature.',
            'timing' =>
                'When casting a qualifying spell that targets only one creature.',
        ],
    ];

    /** @return array<int,array<string,mixed>> */
    public function all(): array
    {
        $options = [];

        foreach (
            self::OPTIONS
            as $key => $option
        ) {
            $option['key'] = $key;
            $options[] = $option;
        }

        return $options;
    }

    /** @return array<string,mixed> */
    public function find(
        string $key
    ): array {
        $key = sanitize_key($key);

        if (! isset(self::OPTIONS[$key])) {
            throw new InvalidArgumentException(
                'Choose a certified Metamagic option.'
            );
        }

        return [
            'key' => $key,
            ...self::OPTIONS[$key],
        ];
    }

    public function cost(
        string $key,
        int $spellLevel = 0
    ): int {
        $option = $this->find($key);

        if (
            $option['cost']
            === 'spell-level'
        ) {
            return max(
                1,
                $spellLevel
            );
        }

        return (int) $option['cost'];
    }
}
