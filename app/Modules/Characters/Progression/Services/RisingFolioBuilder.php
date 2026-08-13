<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\FolioCollection;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\CallingFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\ProficiencyFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\VitalityFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\SpellbookFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\CantripFolio;

defined('ABSPATH') || exit;

final class RisingFolioBuilder
{
    public function __construct(
        private ?VitalityFolio $vitality = null,
        private ?ProficiencyFolio $proficiency = null,
        private ?CallingFolio $calling = null,
        private ?SpellbookFolio $spellbook = null,
        private ?CantripFolio $cantrips = null
    ) {
        $this->vitality ??=
            new VitalityFolio();

        $this->proficiency ??=
            new ProficiencyFolio();

        $this->calling ??=
            new CallingFolio();

        $this->spellbook ??=
            new SpellbookFolio();

        $this->cantrips ??=
            new CantripFolio();
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

        $folios->add(
            $this->calling->build(
                $character,
                $targetLevel
            )
        );

        $spellbook = $this->spellbook->build(
            $character,
            $targetLevel,
            $choices['wizard-spells'] ?? []
        );

        if ($spellbook instanceof \GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\AdvancementFolio) {
            $folios->add($spellbook);
        }

        $cantrips = $this->cantrips->build(
            $character,
            $targetLevel,
            $choices['wizard-cantrips'] ?? []
        );

        if ($cantrips instanceof \GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\AdvancementFolio) {
            $folios->add($cantrips);
        }

        return $folios;
    }
}
