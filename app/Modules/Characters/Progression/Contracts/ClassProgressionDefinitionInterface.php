<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;

defined('ABSPATH') || exit;

interface ClassProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool;

    /**
     * @return array{
     *     class:string,
     *     label:string,
     *     level:int,
     *     automatic:array<int,array<string,mixed>>,
     *     delegated:array<int,array<string,mixed>>,
     *     catalogue_status:string
     * }
     */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array;
}
