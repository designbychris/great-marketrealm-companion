<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Prepared-spell Intelligence half-caster progression for the Artificer Calling.
 */
final class ArtificerSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    public function supports(CharacterClass $class): bool
    {
        return $class->value() === 'artificer';
    }

    /** @return array<string,mixed> */
    public function forLevel(CharacterClass $class, int $level): array
    {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Artificer spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Artificer spellcasting advancement levels must be between 2 and 20.'
            );
        }

        $cantripsKnown = match (true) {
            $level >= 14 => 4,
            $level >= 10 => 3,
            default => 2,
        };

        $previousLevel = $level - 1;
        $previousCantrips = match (true) {
            $previousLevel >= 14 => 4,
            $previousLevel >= 10 => 3,
            default => 2,
        };

        return [
            'class' => 'artificer',
            'model' => 'prepared-spells',
            'level' => $level,
            'spells_known' => null,
            'spells_learned' => 0,
            'spells_prepared_formula' =>
                'half-artificer-level + intelligence-modifier',
            'minimum_spells_prepared' => 1,
            'cantrips_known' => $cantripsKnown,
            'cantrips_learned' => max(0, $cantripsKnown - $previousCantrips),
            'maximum_spell_level' => match (true) {
                $level >= 17 => 5,
                $level >= 13 => 4,
                $level >= 9 => 3,
                $level >= 5 => 2,
                default => 1,
            },
            'spell_choice_key' => 'artificer-prepared-spells',
            'cantrip_choice_key' => 'artificer-cantrips',
        ];
    }
}
