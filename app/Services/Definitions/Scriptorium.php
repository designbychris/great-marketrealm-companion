<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions;

use GreatMarketrealmCompanion\Definitions\Definition;

final class Scriptorium
{
    /**
     * @var array<int, Definition>
     */
    private array $definitions = [];

    public function __construct(
        private Definitions $definitionsService
    ) {
    }

    public function race(
        string $key,
        string $name
    ) {
        $definition = $this->definitionsService
            ->race($key, $name);

        $this->definitions[] = $definition;

        return $definition;
    }

    /**
     * @return array<int, Definition>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }
}
