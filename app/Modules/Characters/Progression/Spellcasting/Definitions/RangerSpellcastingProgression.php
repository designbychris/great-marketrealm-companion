<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Known-spell half-caster progression for the Ranger Calling.
 */
final class RangerSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    /** @var array<int,int> */
    private const SPELLS_KNOWN = [
        2 => 2,
        3 => 3,
        4 => 3,
        5 => 4,
        6 => 4,
        7 => 5,
        8 => 5,
        9 => 6,
        10 => 6,
        11 => 7,
        12 => 7,
        13 => 8,
        14 => 8,
        15 => 9,
        16 => 9,
        17 => 10,
        18 => 10,
        19 => 11,
        20 => 11,
    ];

    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'ranger';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Ranger spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Ranger spellcasting advancement levels must be between 2 and 20.'
            );
        }

        $previous = $level === 2
            ? 0
            : self::SPELLS_KNOWN[$level - 1];

        return [
            'class' => 'ranger',
            'model' => 'known-spells',
            'level' => $level,
            'spells_known' =>
                self::SPELLS_KNOWN[$level],
            'cantrips_known' => 0,
            'spells_learned' =>
                max(
                    0,
                    self::SPELLS_KNOWN[$level]
                    - $previous
                ),
            'cantrips_learned' => 0,
            'maximum_spell_level' =>
                match (true) {
                    $level >= 17 => 5,
                    $level >= 13 => 4,
                    $level >= 9 => 3,
                    $level >= 5 => 2,
                    default => 1,
                },
            'spell_choice_key' =>
                'ranger-spells',
            'cantrip_choice_key' => null,
        ];
    }
}
