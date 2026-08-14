<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

final class PathFolio
{
    public function __construct(
        private ?PathProgressionCatalogue $progression = null,
        private ?PathCandidateCatalogue $candidates = null
    ) {
        $this->progression ??=
            new PathProgressionCatalogue();

        $this->candidates ??=
            new PathCandidateCatalogue();
    }

    /**
     * @param array<string,array<int,string>> $choices
     */
    public function build(
        Character $character,
        int $targetLevel,
        array $choices = []
    ): ?AdvancementFolio {
        if ($character->callingPath()->isChosen()) {
            return null;
        }

        $definition = $this->progression->forClass(
            $character->characterClass()
        );

        if (! is_array($definition)) {
            return null;
        }

        $selectionLevel = (int) (
            $definition['selection_level']
            ?? 0
        );

        /*
         * Catch-up rule:
         * older Characters that already passed their original Path level
         * without a stored Path are asked at their next advancement.
         */
        if ($targetLevel < $selectionLevel) {
            return null;
        }

        $available = $this->candidates->forClass(
            $character->characterClass()
        );

        if ($available === []) {
            return null;
        }

        $choiceKey = (string) (
            $definition['choice_key']
            ?? ''
        );

        $requirement = new ChoiceRequirement(
            $choiceKey,
            ChoiceMode::SINGLE,
            array_column(
                $available,
                'key'
            )
        );

        $selected = $requirement->normalise(
            $choices[$choiceKey]
            ?? []
        );

        $ready = $requirement->satisfiedBy(
            $selected
        );

        $label = (string) (
            $definition['label']
            ?? 'Path of Calling'
        );

        return new AdvancementFolio(
            'path',
            (string) (
                $definition['folio_label']
                ?? 'Path of Calling Folio'
            ),
            $ready
                ? sprintf(
                    '%s has been entered into the pending Guild record.',
                    $label
                )
                : sprintf(
                    'Choose one %s before this advancement can be certified.',
                    $label
                ),
            $ready
                ? FolioStatus::READY
                : FolioStatus::ATTENTION,
            ! $ready,
            [
                'calling' =>
                    $character
                        ->characterClass()
                        ->label(),
                'path_label' => $label,
                'selection_level' =>
                    $selectionLevel,
                'catch_up' =>
                    $character
                        ->level()
                        ->value()
                    >= $selectionLevel,
                'choice_key' =>
                    $requirement->key(),
                'choice_mode' =>
                    $requirement->mode(),
                'choice_minimum' =>
                    $requirement->minimum(),
                'choice_maximum' =>
                    $requirement->maximum(),
                'selected_values' =>
                    $selected,
            ],
            $available
        );
    }
}
