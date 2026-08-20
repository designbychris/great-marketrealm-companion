<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Known-spell full-caster progression for the Bard Calling.
 */
final class BardSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    /** @var array<int,int> */
    private const SPELLS_KNOWN = [
        1 => 4,
        2 => 5,
        3 => 6,
        4 => 7,
        5 => 8,
        6 => 9,
        7 => 10,
        8 => 11,
        9 => 12,
        10 => 14,
        11 => 15,
        12 => 15,
        13 => 16,
        14 => 18,
        15 => 19,
        16 => 19,
        17 => 20,
        18 => 22,
        19 => 22,
        20 => 22,
    ];

    /** @var array<int,int> */
    private const CANTRIPS_KNOWN = [
        1 => 2,
        2 => 2,
        3 => 2,
        4 => 3,
        5 => 3,
        6 => 3,
        7 => 3,
        8 => 3,
        9 => 3,
        10 => 4,
        11 => 4,
        12 => 4,
        13 => 4,
        14 => 4,
        15 => 4,
        16 => 4,
        17 => 4,
        18 => 4,
        19 => 4,
        20 => 4,
    ];

    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'bard';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Bard spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Bard spellcasting advancement levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'bard',
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
                'bard-spells',
            'cantrip_choice_key' =>
                'bard-cantrips',
        ];
    }
}
