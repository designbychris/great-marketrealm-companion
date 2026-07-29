<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;

final class DefinitionFactory
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
