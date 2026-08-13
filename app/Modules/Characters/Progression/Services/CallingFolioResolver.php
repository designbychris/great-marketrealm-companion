<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\AdvancementFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\CallingFolio;

defined('ABSPATH') || exit;

final class CallingFolioResolver
{
    public function __construct(
        private ?CallingFolio $folio = null
    ) {
        $this->folio ??=
            new CallingFolio();
    }

    public function resolve(
        Character $character,
        int $targetLevel
    ): AdvancementFolio {
        return $this->folio->build(
            $character,
            $targetLevel
        );
    }
}
