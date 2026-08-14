<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Services\WizardSpellCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

final class AdvancementChoiceRequirementResolver
{
    public function __construct(
        private ?SpellcastingProgressionCatalogue $spellcasting = null,
        private ?WizardSpellCandidateCatalogue $candidates = null,
        private ?PathProgressionCatalogue $paths = null,
        private ?PathCandidateCatalogue $pathCandidates = null
    ) {
        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();
        $this->candidates ??=
            new WizardSpellCandidateCatalogue();

        $this->paths ??=
            new PathProgressionCatalogue();

        $this->pathCandidates ??=
            new PathCandidateCatalogue();
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

        $path = $this->paths->forClass(
            $character->characterClass()
        );

        if (
            is_array($path)
            && ! $character
                ->callingPath()
                ->isChosen()
            && $targetLevel >= (int) (
                $path['selection_level']
                ?? 0
            )
            && $choiceKey === (string) (
                $path['choice_key']
                ?? ''
            )
        ) {
            $available =
                $this->pathCandidates->forClass(
                    $character
                        ->characterClass()
                );

            if ($available === []) {
                return null;
            }

            return new ChoiceRequirement(
                $choiceKey,
                ChoiceMode::SINGLE,
                array_column(
                    $available,
                    'key'
                )
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
        $required = max(
            0,
            $requested
        );

        if (
            $required < 1
            || $allowed === []
        ) {
            return null;
        }

        /*
         * Do not silently weaken a progression rule merely because the
         * current catalogue is short of options. SpellbookFolio surfaces a
         * clear catalogue-shortfall state when this occurs.
         */
        return new ChoiceRequirement(
            $key,
            ChoiceMode::CHOOSE_N,
            $allowed,
            $required,
            $required
        );
    }
}
