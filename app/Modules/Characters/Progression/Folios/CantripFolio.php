<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementChoiceRequirementResolver;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Services\WizardSpellCandidateCatalogue;

defined('ABSPATH') || exit;

final class CantripFolio
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

        if (
            ! is_array($entry)
            || (int) $entry['cantrips_learned'] < 1
        ) {
            return null;
        }

        $requirement = $this->requirements->resolve(
            $character,
            $targetLevel,
            'wizard-cantrips'
        );

        if ($requirement === null) {
            return null;
        }

        $available = $this->candidates->cantrips($character);
        $selected = $requirement->normalise($selections);
        $ready = $requirement->satisfiedBy($selected);

        return new AdvancementFolio(
            'cantrips',
            'Cantrip Folio',
            $ready
                ? 'The Wizard’s new cantrip has been recorded.'
                : 'Choose the new cantrip entering the Wizard’s permanent repertoire.',
            $ready ? FolioStatus::READY : FolioStatus::ATTENTION,
            ! $ready,
            [
                'target_level' => $targetLevel,
                'choice_key' => $requirement->key(),
                'choice_mode' => $requirement->mode(),
                'choice_minimum' => $requirement->minimum(),
                'choice_maximum' => $requirement->maximum(),
                'selected_values' => $selected,
                'known_cantrips' => count(
                    $character->spellbook()->cantrips()
                ),
            ],
            array_map(
                static fn ($ability): array => [
                    'key' => $ability->id(),
                    'label' => $ability->label(),
                    'detail' => $ability->description(),
                    'spell_level' => 0,
                ],
                $available
            )
        );
    }
}
