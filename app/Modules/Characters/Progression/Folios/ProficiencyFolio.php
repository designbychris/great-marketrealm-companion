<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;

defined('ABSPATH') || exit;

final class ProficiencyFolio
{
    public function build(
        Character $character,
        int $targetLevel
    ): AdvancementFolio {
        $current =
            $character->proficiencyBonus();

        $target =
            ProficiencyBonus::fromLevel(
                Level::fromInt(
                    $targetLevel
                )
            );

        $changes = ! $target->equals(
            $current
        );

        return new AdvancementFolio(
            'proficiency',
            'Proficiency Folio',
            $changes
                ? sprintf(
                    'Proficiency rises from %s to %s.',
                    $current->signed(),
                    $target->signed()
                )
                : sprintf(
                    'Proficiency remains %s at Level %d.',
                    $current->signed(),
                    $targetLevel
                ),
            FolioStatus::READY,
            false,
            [
                'current' => $current->signed(),
                'target' => $target->signed(),
                'changes' => $changes,
            ]
        );
    }
}
