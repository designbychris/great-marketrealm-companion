<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Known-spell progression for the Sorcerer Calling.
 */
final class SorcererSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    /** @var array<int,int> */
    private const SPELLS_KNOWN = [
        1 => 2,
        2 => 3,
        3 => 4,
        4 => 5,
        5 => 6,
        6 => 7,
        7 => 8,
        8 => 9,
        9 => 10,
        10 => 11,
        11 => 12,
        12 => 12,
        13 => 13,
        14 => 13,
        15 => 14,
        16 => 14,
        17 => 15,
        18 => 15,
        19 => 15,
        20 => 15,
    ];

    /** @var array<int,int> */
    private const CANTRIPS_KNOWN = [
        1 => 4,
        2 => 4,
        3 => 4,
        4 => 5,
        5 => 5,
        6 => 5,
        7 => 5,
        8 => 5,
        9 => 5,
        10 => 6,
        11 => 6,
        12 => 6,
        13 => 6,
        14 => 6,
        15 => 6,
        16 => 6,
        17 => 6,
        18 => 6,
        19 => 6,
        20 => 6,
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
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Sorcerer spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Sorcerer spellcasting advancement levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'sorcerer',
            'model' => 'known-spells',
            'level' => $level,
            'spells_known' =>
                self::SPELLS_KNOWN[$level],
            'cantrips_known' =>
                self::CANTRIPS_KNOWN[$level],
            'spells_learned' =>
                max(
                    0,
                    self::SPELLS_KNOWN[$level]
                    - self::SPELLS_KNOWN[$level - 1]
                ),
            'cantrips_learned' =>
                max(
                    0,
                    self::CANTRIPS_KNOWN[$level]
                    - self::CANTRIPS_KNOWN[$level - 1]
                ),
            'maximum_spell_level' =>
                min(
                    9,
                    intdiv($level + 1, 2)
                ),
            'spell_choice_key' =>
                'sorcerer-spells',
            'cantrip_choice_key' =>
                'sorcerer-cantrips',
        ];
    }
}
