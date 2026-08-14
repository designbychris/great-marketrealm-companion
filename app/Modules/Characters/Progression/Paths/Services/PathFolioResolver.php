<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\AdvancementFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathFolio;

defined('ABSPATH') || exit;

final class PathFolioResolver
{
    public function __construct(
        private ?PathFolio $folio = null
    ) {
        $this->folio ??=
            new PathFolio();
    }

    /**
     * @param array<string,array<int,string>> $choices
     */
    public function resolve(
        Character $character,
        int $targetLevel,
        array $choices = []
    ): ?AdvancementFolio {
        return $this->folio->build(
            $character,
            $targetLevel,
            $choices
        );
    }
}
