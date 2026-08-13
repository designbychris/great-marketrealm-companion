<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts\SpellcastingProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class WizardSpellcastingProgression implements SpellcastingProgressionDefinitionInterface
{
    public function supports(CharacterClass $class): bool
    {
        return $class->value() === 'wizard';
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Wizard spellcasting cannot resolve another Calling.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Wizard spellcasting advancement levels must be between 2 and 20.'
            );
        }

        return [
            'class' => 'wizard',
            'model' => 'spellbook',
            'level' => $level,
            'spells_learned' => 2,
            'cantrips_learned' => in_array(
                $level,
                [4, 10],
                true
            ) ? 1 : 0,
            'maximum_spell_level' => min(
                9,
                intdiv($level + 1, 2)
            ),
            'spell_choice_key' => 'wizard-spells',
            'cantrip_choice_key' => 'wizard-cantrips',
        ];
    }
}
