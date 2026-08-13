<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Services\WizardSpellCandidateCatalogue;

defined('ABSPATH') || exit;

final class AdvancementChoiceRequirementResolver
{
    public function __construct(
        private ?SpellcastingProgressionCatalogue $spellcasting = null,
        private ?WizardSpellCandidateCatalogue $candidates = null
    ) {
        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();
        $this->candidates ??=
            new WizardSpellCandidateCatalogue();
    }

    public function resolve(
        Character $character,
        int $targetLevel,
        string $choiceKey
    ): ?ChoiceRequirement {
        if ($choiceKey === 'vitality-hit-points') {
            return new ChoiceRequirement(
                'vitality-hit-points',
                ChoiceMode::SINGLE,
                ['average', 'roll']
            );
        }

        $progression = $this->spellcasting->forLevel(
            $character->characterClass(),
            $targetLevel
        );

        if (! is_array($progression)) {
            return null;
        }

        if ($choiceKey === 'wizard-spells') {
            $available = $this->candidates->spells(
                $character,
                (int) $progression['maximum_spell_level']
            );

            return $this->chooseN(
                'wizard-spells',
                array_map(
                    static fn ($ability): string =>
                        $ability->id(),
                    $available
                ),
                (int) $progression['spells_learned']
            );
        }

        if ($choiceKey === 'wizard-cantrips') {
            $available = $this->candidates->cantrips(
                $character
            );

            return $this->chooseN(
                'wizard-cantrips',
                array_map(
                    static fn ($ability): string =>
                        $ability->id(),
                    $available
                ),
                (int) $progression['cantrips_learned']
            );
        }

        return null;
    }

    /** @param array<int,string> $allowed */
    private function chooseN(
        string $key,
        array $allowed,
        int $requested
    ): ?ChoiceRequirement {
        $required = min(
            max(0, $requested),
            count($allowed)
        );

        if ($required < 1) {
            return null;
        }

        return new ChoiceRequirement(
            $key,
            ChoiceMode::CHOOSE_N,
            $allowed,
            $required,
            $required
        );
    }
}
