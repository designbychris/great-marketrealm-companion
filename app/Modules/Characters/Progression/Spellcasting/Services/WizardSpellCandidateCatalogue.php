<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

final class WizardSpellCandidateCatalogue
{
    public function __construct(
        private ?ArcaneAbilityCatalogue $arcana = null
    ) {
        $this->arcana ??=
            new ArcaneAbilityCatalogue();
    }

    /** @return array<int,ArcaneAbilityDefinition> */
    public function spells(
        Character $character,
        int $maximumSpellLevel
    ): array {
        return array_values(
            array_filter(
                $this->arcana->forClass('wizard'),
                static fn (ArcaneAbilityDefinition $ability): bool =>
                    $ability->kind() === 'spell'
                    && $ability->spellLevel() > 0
                    && $ability->spellLevel() <= $maximumSpellLevel
                    && ! $character->spellbook()->knows(
                        $ability->id()
                    )
            )
        );
    }

    /** @return array<int,ArcaneAbilityDefinition> */
    public function cantrips(Character $character): array
    {
        return array_values(
            array_filter(
                $this->arcana->forClass('wizard'),
                static fn (ArcaneAbilityDefinition $ability): bool =>
                    $ability->kind() === 'cantrip'
                    && ! $character->spellbook()->knows(
                        $ability->id()
                    )
            )
        );
    }
}
