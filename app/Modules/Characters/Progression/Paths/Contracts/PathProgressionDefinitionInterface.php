<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;

defined('ABSPATH') || exit;

interface PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool;

    /**
     * @return array{
     *     class:string,
     *     label:string,
     *     folio_label:string,
     *     choice_key:string,
     *     selection_level:int,
     *     description:string
     * }
     */
    public function definition(
        CharacterClass $class
    ): array;
}
