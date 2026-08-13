<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;

defined('ABSPATH') || exit;

interface SpellcastingProgressionDefinitionInterface
{
    public function supports(CharacterClass $class): bool;

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array;
}
