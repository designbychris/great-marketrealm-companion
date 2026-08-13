<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;

defined('ABSPATH') || exit;

final class VitalityFolio
{
    /**
     * @param array<int,string> $selections
     */
    public function build(
        Character $character,
        int $targetLevel,
        array $selections = []
    ): AdvancementFolio {
        $hitDie =
            $character->characterClass()->hitDie();

        $constitution = $character
            ->abilityScores()
            ->constitution()
            ->modifier();

        $average = max(
            1,
            1 + intdiv(
                $hitDie,
                2
            ) + $constitution
        );

        $requirement = new ChoiceRequirement(
            'vitality-hit-points',
            ChoiceMode::SINGLE,
            [
                'average',
                'roll',
            ]
        );

        $selected = $requirement->normalise(
            $selections
        );

        $ready = $requirement->satisfiedBy(
            $selected
        );

        return new AdvancementFolio(
            'vitality',
            'Vitality Folio',
            $ready
                ? 'Hit point advancement method recorded.'
                : 'Choose how this level increases maximum hit points.',
            $ready
                ? FolioStatus::READY
                : FolioStatus::ATTENTION,
            ! $ready,
            [
                'target_level' => $targetLevel,
                'hit_die' => 'd' . $hitDie,
                'constitution_modifier' =>
                    $constitution,
                'average_gain' => $average,
                'current_maximum' =>
                    $character
                        ->hitPoints()
                        ->maximum(),
                'choice_key' =>
                    $requirement->key(),
                'choice_mode' =>
                    $requirement->mode(),
                'choice_minimum' =>
                    $requirement->minimum(),
                'choice_maximum' =>
                    $requirement->maximum(),
                'selected' =>
                    $selected[0] ?? '',
                'selected_values' => $selected,
            ],
            [
                [
                    'key' => 'average',
                    'label' => 'Take the class average',
                    'value' => $average,
                ],
                [
                    'key' => 'roll',
                    'label' => 'Roll the class Hit Die',
                    'die' => 'd' . $hitDie,
                    'modifier' => $constitution,
                ],
            ]
        );
    }
}
