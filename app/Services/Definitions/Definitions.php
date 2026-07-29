<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;

/**
 * Creates game-content definitions.
 *
 * This service provides the main authoring API for races,
 * classes, monsters, spells and other game content.
 */
final class Definitions
{
    public function race(
        string $key,
        string $name
    ): RaceDefinition {
        return new RaceDefinition(
            key: $key,
            name: $name,
        );
    }
}
