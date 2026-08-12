<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

final class VitalityFolio
{
    public function build(
        Character $character,
        int $targetLevel
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

        return new AdvancementFolio(
            'vitality',
            'Vitality Folio',
            'Choose how this level increases maximum hit points.',
            FolioStatus::ATTENTION,
            true,
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
