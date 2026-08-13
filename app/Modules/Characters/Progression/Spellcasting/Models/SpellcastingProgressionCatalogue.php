<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\WizardSpellcastingProgression;

final class SpellcastingProgressionCatalogue
{
    public function supports(CharacterClass $class): bool
    {
        return (new WizardSpellcastingProgression())
            ->supports($class);
    }

    /** @return array<string,mixed>|null */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): ?array {
        $wizard = new WizardSpellcastingProgression();

        return $wizard->supports($class)
            ? $wizard->forLevel($class, $level)
            : null;
    }
}
