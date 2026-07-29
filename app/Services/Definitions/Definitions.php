<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;

defined('ABSPATH') || exit;

/**
 * The Definitions Service.
 *
 * Provides the central entry point for creating and authoring
 * game-content definitions.
 *
 * @since 0.3.0
 */
final class Definitions
{
    /**
     * Create a new Scriptorium authoring session.
     */
    public function scriptorium(): Scriptorium
    {
        return new Scriptorium($this);
    }

    /**
     * Create a race definition.
     */
    public function race(
        string $key,
        string $name
    ): RaceDefinition {
        return new RaceDefinition(
            key: $key,
            name: $name
        );
    }
}
