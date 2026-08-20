<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Prepared-spell full-caster progression for the Druid Calling.
 */
final class DruidSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'druid';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Druid spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Druid spellcasting advancement levels must be between 2 and 20.'
            );
        }

        $cantripsKnown = match (true) {
            $level >= 10 => 4,
            $level >= 4 => 3,
            default => 2,
        };

        $previousCantrips = match (true) {
            $level - 1 >= 10 => 4,
            $level - 1 >= 4 => 3,
            default => 2,
        };

        return [
            'class' => 'druid',
            'model' => 'prepared-spells',
            'level' => $level,
            'spells_known' => null,
            'spells_learned' => 0,
            'spells_prepared_formula' =>
                'druid-level + wisdom-modifier',
            'minimum_spells_prepared' => 1,
            'cantrips_known' =>
                $cantripsKnown,
            'cantrips_learned' =>
                max(
                    0,
                    $cantripsKnown
                    - $previousCantrips
                ),
            'maximum_spell_level' =>
                min(
                    9,
                    intdiv($level + 1, 2)
                ),
            'spell_choice_key' =>
                'druid-prepared-spells',
            'cantrip_choice_key' =>
                'druid-cantrips',
        ];
    }
}
