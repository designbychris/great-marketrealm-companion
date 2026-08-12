<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\FolioCollection;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\ProficiencyFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\VitalityFolio;

defined('ABSPATH') || exit;

final class RisingFolioBuilder
{
    public function __construct(
        private ?VitalityFolio $vitality = null,
        private ?ProficiencyFolio $proficiency = null
    ) {
        $this->vitality ??=
            new VitalityFolio();

        $this->proficiency ??=
            new ProficiencyFolio();
    }

    /**
     * @param array<string,array<int,string>> $choices
     */
    public function forAdvancement(
        Character $character,
        int $targetLevel,
        array $choices = []
    ): FolioCollection {
        $folios = new FolioCollection();

        $folios->add(
            $this->vitality->build(
                $character,
                $targetLevel,
                $choices['vitality-hit-points']
                    ?? []
            )
        );

        $folios->add(
            $this->proficiency->build(
                $character,
                $targetLevel
            )
        );

        return $folios;
    }
}
