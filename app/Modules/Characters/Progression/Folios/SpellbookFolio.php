<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementChoiceRequirementResolver;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Services\WizardSpellCandidateCatalogue;

defined('ABSPATH') || exit;

final class SpellbookFolio
{
    public function __construct(
        private ?SpellcastingProgressionCatalogue $progression = null,
        private ?WizardSpellCandidateCatalogue $candidates = null,
        private ?AdvancementChoiceRequirementResolver $requirements = null
    ) {
        $this->progression ??= new SpellcastingProgressionCatalogue();
        $this->candidates ??= new WizardSpellCandidateCatalogue();
        $this->requirements ??= new AdvancementChoiceRequirementResolver();
    }

    /** @param array<int,string> $selections */
    public function build(
        Character $character,
        int $targetLevel,
        array $selections = []
    ): ?AdvancementFolio {
        $entry = $this->progression->forLevel(
            $character->characterClass(),
            $targetLevel
        );

        if (! is_array($entry)) {
            return null;
        }

        $requirement = $this->requirements->resolve(
            $character,
            $targetLevel,
            'wizard-spells'
        );

        if ($requirement === null) {
            return null;
        }

        $available = $this->candidates->spells(
            $character,
            (int) $entry['maximum_spell_level']
        );

        $selected = $requirement->normalise($selections);
        $ready = $requirement->satisfiedBy($selected);

        return new AdvancementFolio(
            'spellbook',
            'Spellbook Folio',
            $ready
                ? 'The Wizard’s new spell studies have been recorded.'
                : sprintf(
                    'Choose %d new spells for the Wizard’s spellbook.',
                    $requirement->minimum()
                ),
            $ready ? FolioStatus::READY : FolioStatus::ATTENTION,
            ! $ready,
            [
                'target_level' => $targetLevel,
                'maximum_spell_level' =>
                    (int) $entry['maximum_spell_level'],
                'choice_key' => $requirement->key(),
                'choice_mode' => $requirement->mode(),
                'choice_minimum' => $requirement->minimum(),
                'choice_maximum' => $requirement->maximum(),
                'selected_values' => $selected,
                'known_spells' => count(
                    $character->spellbook()->spells()
                ),
            ],
            array_map(
                static fn ($ability): array => [
                    'key' => $ability->id(),
                    'label' => $ability->label(),
                    'detail' => $ability->description(),
                    'spell_level' => $ability->spellLevel(),
                ],
                $available
            )
        );
    }
}
