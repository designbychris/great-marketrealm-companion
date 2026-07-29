<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;

defined('ABSPATH') || exit;

/**
 * The Scriptorium.
 *
 * Provides the central authoring API for game-content definitions.
 *
 * @since 0.3.0
 */
final class Definitions
{
    /**
     * Create a race definition.
     */
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
